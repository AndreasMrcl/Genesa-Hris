<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Employee;
use App\Models\CompanyPayrollConfig;
use App\Models\GlobalBpjs;
use App\Models\GlobalPtkp;
use App\Models\GlobalTerRate;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Exports\PayrollExport;
use Maatwebsite\Excel\Facades\Excel;

class PayrollController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $userCompany = Auth::user()->compani;

        if (!$userCompany) {
            return redirect()->route('addcompany');
        }

        $status = $userCompany->status;

        if ($status !== 'Settlement') {
            return redirect()->route('login');
        }

        $page = request()->get('page', 1);

        $cacheKey = 'payroll_' . $userCompany->id . '_page_' . $page;

        $batches = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userCompany) {
            // Query Grouping: Mengelompokkan berdasarkan tanggal start & end
            return $userCompany->payrolls()
                ->select(
                    'pay_period_start',
                    'pay_period_end',
                    DB::raw('count(*) as total_employees'),
                    DB::raw('sum(net_salary) as total_spent'),
                    DB::raw('max(status) as status'), // Ambil salah satu status
                    DB::raw('max(created_at) as created_at') // Untuk sorting
                )
                ->groupBy('pay_period_start', 'pay_period_end')
                ->latest('created_at')
                ->paginate(10);
        });

        return view('payroll', compact('batches'));
    }

    public function period($start, $end)
    {
        $userCompany = Auth::user()->compani;

        $cacheKey = 'payroll_period_' . $userCompany->id . '_' . $start . '_' . $end;

        $payrolls = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userCompany, $start, $end) {
            return $userCompany->payrolls()
                ->with('employee')
                ->where('pay_period_start', $start)
                ->where('pay_period_end', $end)
                ->get();
        });

        if ($payrolls->isEmpty()) {
            return redirect()->route('payroll')->withErrors(['msg' => 'Payroll data not found.']);
        }

        return view('payrollPeriod', compact('payrolls', 'start', 'end'));
    }

    // Menampilkan Form "Run Payroll"
    public function create()
    {
        $userCompany = Auth::user()->compani;

        $availablePeriods = $userCompany->attendances()
            ->select('period_start', 'period_end')
            ->distinct()
            ->orderBy('period_end', 'desc')
            ->get();

        return view('payrollCreate', compact('availablePeriods'));
    }

    public function store(Request $request)
    {
        $userCompany = Auth::user()->compani;

        $request->validate([
            'selected_period' => 'required|string',
        ]);

        [$start, $end] = explode('|', $request->selected_period);

        // Cek Duplicate
        $exists = $userCompany->payrolls()
            ->where('pay_period_start', $start)
            ->where('pay_period_end', $end)
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => "Payroll for period $start to $end already exists!"]);
        }

        // LOAD CONFIGURATIONS
        $globalBpjs = GlobalBpjs::first();
        $companyConfig = CompanyPayrollConfig::where('compani_id', $userCompany->id)->first();

        // Safety fallback jika config belum diset
        if (!$companyConfig) return back()->withErrors(['msg' => 'Company Settings not found.']);
        if (!$globalBpjs) return back()->withErrors(['msg' => 'Global BPJS Settings not found.']);

        $employees = $userCompany->employees()
            ->whereHas('attendances', function($q) use ($start, $end) {
                $q->where('period_start', $start)->where('period_end', $end);
            })
            ->with(['allowEmps.allow', 'deductEmps.deduct', 'attendances' => function($q) use ($start, $end) {
                $q->where('period_start', $start)->where('period_end', $end);
            }])
            ->get();

        if ($employees->isEmpty()) {
            return back()->withErrors(['msg' => "No attendance data found for period $start to $end."]);
        }

        DB::beginTransaction();

        try {
            $countProcessed = 0;
            $totalExpenseForLog = 0;

            foreach ($employees as $emp) {

                $attendanceRecord = $emp->attendances->first();
                $daysPresent = $attendanceRecord ? $attendanceRecord->total_present : 0;
                
                // === A. VARIABEL DASAR ===
                $baseSalary = $emp->base_salary ?? 0;
                $totalAllowance = 0;
                $totalDeduction = 0;
                $detailsToSave = [];
                // taxableIncomeBase = Gaji Pokok + Tunjangan Tetap/Harian yang Kena Pajak
                $taxableIncomeBase = $baseSalary;

                $detailsToSave[] = ['name' => 'Base Salary', 'category' => 'base', 'amount' => $baseSalary];

                // === B. HITUNG ALLOWANCES (TUNJANGAN) ===
                foreach ($emp->allowEmps as $assign) {
                    $master = $assign->allow;
                    $amount = 0;

                    if ($master->type == 'fixed') {
                        $amount = $assign->amount;
                    } elseif ($master->type == 'daily') {
                        $amount = $assign->amount * $daysPresent;
                    } elseif ($master->type == 'one_time') {
                        $amount = $assign->amount;
                    }

                    $totalAllowance += $amount;

                    // Jika Tunjangan Kena Pajak, tambahkan ke Dasar Pajak
                    if ($master->is_taxable) $taxableIncomeBase += $amount;

                    $detailsToSave[] = [
                        'name' => $master->name,
                        'category' => 'allowance',
                        'amount' => $amount,
                    ];
                }

                // === C. HITUNG BPJS (Sesuai Referensi Talenta) ===
                $bpjsBasis = $baseSalary;

                $bpjsEmpDeduction = 0; // Potongan Gaji Karyawan (Kurangi THP)
                $companyBenefit = 0;   // Tunjangan Perusahaan (Menambah Bruto Pajak)

                // Cek kepesertaan (Default true jika null)
                $partKes = $emp->participates_bpjs_kes ?? true;
                $partTk  = $emp->participates_bpjs_tk ?? true;
                $partJp  = $emp->participates_bpjs_jp ?? true;

                // 1. BPJS Kesehatan
                if ($companyConfig->bpjs_kes_active && $partKes) {
                    $basisKes = min($bpjsBasis, $globalBpjs->kes_cap_amount);

                    // Emp (1%) -> Potong Gaji
                    $kesEmp = $basisKes * ($globalBpjs->kes_emp_percent / 100);
                    $bpjsEmpDeduction += $kesEmp;
                    $detailsToSave[] = ['name' => 'BPJS Kesehatan (1%)', 'category' => 'deduction', 'amount' => $kesEmp];

                    // Comp (4%) -> Menambah Pajak (Benefit)
                    $kesComp = $basisKes * ($globalBpjs->kes_comp_percent / 100);
                    $companyBenefit += $kesComp;
                }

                // 2. BPJS TK (JHT, JKK, JKM)
                if ($companyConfig->bpjs_tk_active && $partTk) {
                    // JKK (Perusahaan) -> Menambah Pajak
                    $jkk = $bpjsBasis * ($companyConfig->bpjs_jkk_rate / 100);
                    $companyBenefit += $jkk;

                    // JKM (Perusahaan) -> Menambah Pajak
                    $jkm = $bpjsBasis * ($globalBpjs->jkm_comp_percent / 100);
                    $companyBenefit += $jkm;

                    // JHT Employee (2%) -> Potong Gaji
                    $jhtEmp = $bpjsBasis * ($globalBpjs->jht_emp_percent / 100);
                    $bpjsEmpDeduction += $jhtEmp;
                    $detailsToSave[] = ['name' => 'JHT (2%)', 'category' => 'deduction', 'amount' => $jhtEmp];
                }

                // 3. Jaminan Pensiun
                if ($companyConfig->bpjs_tk_active && $partJp) {
                    $basisJp = min($bpjsBasis, $globalBpjs->jp_cap_amount);
                    $jpEmp = $basisJp * ($globalBpjs->jp_emp_percent / 100);
                    $bpjsEmpDeduction += $jpEmp;
                    $detailsToSave[] = ['name' => 'Jaminan Pensiun (1%)', 'category' => 'deduction', 'amount' => $jpEmp];
                }

                // Tambahkan total potongan BPJS ke total deduction payroll
                $totalDeduction += $bpjsEmpDeduction;


                // === D. HITUNG PPH 21 (TER) ===
                $ptkpStatus = $emp->ptkp_status ?? 'TK/0';
                $ptkpRule = GlobalPtkp::where('code', $ptkpStatus)->first();

                if ($ptkpRule) {
                    $taxMethod = $companyConfig->tax_method ?? 'GROSS';

                    // Penghasilan Bruto Sebulan = (Gaji + Tunjangan) + (JKK + JKM + BPJS Kes Perusahaan)
                    $brutoDasar = $taxableIncomeBase + $companyBenefit;

                    if ($taxMethod == 'GROSS') {
                        // Pajak dipotong dari gaji
                        $pph21 = $this->calculatePph21TER($brutoDasar, $ptkpRule->ter_category);
                        if ($pph21 > 0) {
                            $totalDeduction += $pph21;
                            $detailsToSave[] = ['name' => 'PPh 21 (Gross)', 'category' => 'deduction', 'amount' => $pph21];
                        }
                    } elseif ($taxMethod == 'NET') {
                        // Pajak ditanggung perusahaan (Tampil info saja, tidak mengurangi gaji)
                        $pph21 = $this->calculatePph21TER($brutoDasar, $ptkpRule->ter_category);
                        if ($pph21 > 0) {
                            $detailsToSave[] = ['name' => 'PPh 21 (Ditanggung Perusahaan)', 'category' => 'deduction', 'amount' => 0,];
                        }
                    } elseif ($taxMethod == 'GROSS UP') { // Perbaikan: GROSS_UP (underscore) sesuai migration
                        // Cari Tunjangan Pajak
                        $tunjanganPajak = 0;
                        $iterasiBruto = $brutoDasar;

                        // Iterasi mencari angka tunjangan pajak yang pas
                        for ($i = 0; $i < 50; $i++) {
                            $hitungPajak = $this->calculatePph21TER($iterasiBruto, $ptkpRule->ter_category);
                            $selisih = $hitungPajak - $tunjanganPajak;
                            if (abs($selisih) < 1) {
                                $tunjanganPajak = $hitungPajak;
                                break;
                            }

                            $tunjanganPajak = $hitungPajak;
                            $iterasiBruto = $brutoDasar + $tunjanganPajak;
                        }

                        if ($tunjanganPajak > 0) {
                            $totalAllowance += $tunjanganPajak;
                            $totalDeduction += $tunjanganPajak;

                            $detailsToSave[] = ['name' => 'Tunjangan PPh 21', 'category' => 'allowance', 'amount' => $tunjanganPajak];
                            $detailsToSave[] = ['name' => 'Potongan PPh 21', 'category' => 'deduction', 'amount' => $tunjanganPajak];
                        }
                    } elseif ($taxMethod == 'GROSS_UP') { // Handle jika string di DB pakai underscore
                        // (Copy logika GROSS UP di atas kesini jika perlu, atau pastikan string enum sama)
                        // Logika sama seperti blok GROSS UP di atas
                        $tunjanganPajak = 0;
                        $iterasiBruto = $brutoDasar;
                        for ($i = 0; $i < 50; $i++) {
                            $hitungPajak = $this->calculatePph21TER($iterasiBruto, $ptkpRule->ter_category);
                            if (abs($hitungPajak - $tunjanganPajak) < 1) {
                                $tunjanganPajak = $hitungPajak;
                                break;
                            }
                            $tunjanganPajak = $hitungPajak;
                            $iterasiBruto = $brutoDasar + $tunjanganPajak;
                        }
                        if ($tunjanganPajak > 0) {
                            $totalAllowance += $tunjanganPajak;
                            $totalDeduction += $tunjanganPajak;
                            $detailsToSave[] = ['name' => 'Tunjangan PPh 21', 'category' => 'allowance', 'amount' => $tunjanganPajak];
                            $detailsToSave[] = ['name' => 'Potongan PPh 21', 'category' => 'deduction', 'amount' => $tunjanganPajak];
                        }
                    }
                }

                // === E. HITUNG POTONGAN LAINNYA ===
                foreach ($emp->deductEmps as $assign) {
                    $master = $assign->deduct;
                    $amount = $assign->amount;
                    $totalDeduction += $amount;
                    $detailsToSave[] = ['name' => $master->name, 'category' => 'deduction', 'amount' => $amount];
                }

                // === F. FINALISASI ===
                $netSalary = $baseSalary + $totalAllowance - $totalDeduction;
                $totalExpenseForLog += $netSalary; // Tambahkan ke total expense batch

                // Simpan Header
                $payroll = Payroll::create([
                    'compani_id' => $userCompany->id,
                    'employee_id' => $emp->id,
                    'pay_period_start' => $start,
                    'pay_period_end' => $end,
                    'base_salary' => $baseSalary,
                    'total_allowances' => $totalAllowance,
                    'total_deductions' => $totalDeduction,
                    'net_salary' => $netSalary,
                    'status' => 'pending',
                ]);

                // Simpan Detail
                foreach ($detailsToSave as $detail) {
                    PayrollDetail::create([
                        'payroll_id' => $payroll->id,
                        'name' => $detail['name'],
                        'category' => $detail['category'],
                        'amount' => $detail['amount'],
                    ]);
                }

                $countProcessed++;
            }

            // --- LOG ACTIVITY (TAMBAHAN) ---
            $formattedStart = \Carbon\Carbon::parse($request->pay_period_start)->format('d M Y');
            $formattedEnd = \Carbon\Carbon::parse($request->pay_period_end)->format('d M Y');
            $formattedExpense = number_format($totalExpenseForLog, 0, ',', '.');

            $this->logActivity(
                'Generate Payroll',
                "Memproses penggajian periode {$formattedStart} s/d {$formattedEnd} untuk {$countProcessed} karyawan. Total Pengeluaran: Rp {$formattedExpense}",
                $userCompany->id
            );

            DB::commit();
            Cache::forget('payroll_' . $userCompany->id . '_page_1');

            return redirect()->route('payroll')->with('success', "Generated $countProcessed slips for period $start to $end.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Calculation Error: ' . $e->getMessage() . ' Line: ' . $e->getLine()]);
        }
    }

    // Helper Function
    private function calculatePph21TER($grossIncome, $category)
    {
        $rate = GlobalTerRate::where('ter_category', $category)
            ->where('gross_income_min', '<=', $grossIncome)
            ->where(function ($q) use ($grossIncome) {
                $q->where('gross_income_max', '>=', $grossIncome)
                    ->orWhereNull('gross_income_max');
            })
            ->first();

        if ($rate) {
            return $grossIncome * ($rate->rate_percentage / 100);
        }
        return 0;
    }

    // Menampilkan Detail Slip Gaji
    public function show($id)
    {
        $userCompany = Auth::user()->compani;

        $cacheKey = 'payroll_detail_' . $id;

        $payroll = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($id, $userCompany) {
            return $userCompany->payrolls()
                ->with(['employee', 'payrollDetails'])
                ->findOrFail($id);
        });

        return view('payrollShow', compact('payroll'));
    }

    public function destroyPeriod(Request $request)
    {
        $userCompany = Auth::user()->compani;
        $start = $request->start;
        $end = $request->end;

        $deleted = $userCompany->payrolls()
            ->where('pay_period_start', $start)
            ->where('pay_period_end', $end)
            ->delete();

        $formattedStart = \Carbon\Carbon::parse($start)->format('d M Y');
        $formattedEnd = \Carbon\Carbon::parse($end)->format('d M Y');
        $this->logActivity('Delete Payroll Batch', "Menghapus seluruh data gaji periode {$formattedStart} s/d {$formattedEnd}", $userCompany->id);

        // Hapus Cache Index
        Cache::forget('payroll_' . $userCompany->id . '_page_1');

        // Hapus Cache Detail Periode
        Cache::forget('payroll_period_' . $userCompany->id . '_' . $start . '_' . $end);

        return redirect()->route('payroll')->with('success', "Deleted payroll batch ($deleted records).");
    }

    public function destroy($id)
    {
        $userCompany = Auth::user()->compani;
        $payroll = $userCompany->payrolls()->with('employee')->findOrFail($id);

        // Simpan info tanggal sebelum dihapus untuk clear cache periode
        $start = $payroll->pay_period_start;
        $end = $payroll->pay_period_end;
        $employeeName = $payroll->employee->name;

        $payroll->delete();

        $this->logActivity('Delete Payroll Slip', "Menghapus slip gaji milik {$employeeName} untuk periode {$start}", $userCompany->id);

        Cache::forget('payroll_' . $userCompany->id . '_page_1');
        Cache::forget('payroll_detail_' . $id); // Cache Slip itu sendiri
        Cache::forget('payroll_period_' . $userCompany->id . '_' . $start . '_' . $end); // Cache List Periode

        return back()->with('success', 'Single payroll record deleted.');
    }

    public function exportExcel(Request $request)
    {
        $userCompany = Auth::user()->compani;

        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date',
        ]);

        $start = $request->start;
        $end = $request->end;

        $exists = $userCompany->payrolls()
            ->where('pay_period_start', $start)
            ->where('pay_period_end', $end)
            ->exists();

        if (!$exists) {
            return redirect()->route('payroll')->withErrors(['msg' => 'No payroll data found for this period to export.']);
        }

        $this->logActivity('Export Payroll', "Mengunduh laporan Excel untuk periode {$start} s/d {$end}", $userCompany->id);

        $filename = 'Payroll_Rekap_' . $start . '_to_' . $end . '.xlsx';

        return Excel::download(new PayrollExport($userCompany->id, $start, $end), $filename);
    }

    private function logActivity($type, $description, $companyId)
    {
        ActivityLog::create([
            'user_id'       => Auth::id(),
            'compani_id'    => $companyId,
            'activity_type' => $type,
            'description'   => $description,
            'created_at'    => now(),
        ]);

        Cache::tags(['activities_' . $companyId])->flush();
    }
}