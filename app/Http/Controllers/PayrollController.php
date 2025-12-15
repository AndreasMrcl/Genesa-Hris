<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Employee;
use App\Models\CompanyPayrollConfig;
use App\Models\GlobalPtkp;
use App\Models\GlobalTerRate;
use App\Models\ActivityLog;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Exports\PayrollExport;
use App\Exports\PayrollReportExport;
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

        $batches = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return $userCompany->payrolls()
                ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
                ->select(
                    'payrolls.pay_period_start', 
                    'payrolls.pay_period_end', 
                    DB::raw('count(distinct employees.branch_id) as total_branches'), 
                    DB::raw('sum(payrolls.net_salary) as total_spent'),
                    DB::raw('max(payrolls.status) as status'),
                    DB::raw('max(payrolls.created_at) as created_at')
                )
                ->groupBy('payrolls.pay_period_start', 'payrolls.pay_period_end')
                ->latest('payrolls.created_at')
                ->paginate(10);
        });

        return view('payroll', compact('batches'));
    }

    public function period($start, $end)
    {
        $userCompany = Auth::user()->compani;

        $cacheKey = 'payroll_period_' . $userCompany->id . '_' . $start . '_' . $end;

        $branchStats = Cache::remember($cacheKey, 180, function () use ($userCompany, $start, $end) {
            return Payroll::query()
                ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
                ->join('branches', 'employees.branch_id', '=', 'branches.id')
                ->where('payrolls.compani_id', $userCompany->id)
                ->where('payrolls.pay_period_start', $start)
                ->where('payrolls.pay_period_end', $end)
                ->select(
                    'branches.id',
                    'branches.name',
                    'branches.category',
                    DB::raw('count(payrolls.id) as employee_count'),
                    DB::raw('sum(payrolls.net_salary) as total_expense')
                )
                ->groupBy('branches.id', 'branches.name', 'branches.category')
                ->orderBy('branches.name')
                ->get();
        });

        if ($branchStats->isEmpty()) {
            return redirect()->route('payroll')->withErrors(['msg' => 'Payroll data not found.']);
        }

        return view('payrollBranches', compact('branchStats', 'start', 'end'));
    }

    public function branch($start, $end, $branchId)
    {
        $userCompany = Auth::user()->compani;
        
        $cacheKey = 'payroll_emp_' . $userCompany->id . '_' . $start . '_' . $end . '_' . $branchId;

        $payrolls = Cache::remember($cacheKey, 180, function () use ($userCompany, $start, $end, $branchId) {
            return Payroll::with(['employee', 'employee.position'])
                ->where('compani_id', $userCompany->id)
                ->where('pay_period_start', $start)
                ->where('pay_period_end', $end)
                ->whereHas('employee', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->get();
        });

        $branchName = Branch::where('id', $branchId)->value('name');

        return view('payrollEmp', compact('payrolls', 'start', 'end', 'branchName', 'branchId'));
    }

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

        $exists = $userCompany->payrolls()
            ->where('pay_period_start', $start)
            ->where('pay_period_end', $end)
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => "Payroll for period $start to $end already exists!"]);
        }

        $companyConfig = CompanyPayrollConfig::where('compani_id', $userCompany->id)->first();

        if (!$companyConfig) return back()->withErrors(['msg' => 'Company Settings not found.']);
        if ($companyConfig->ump_amount <= 0) return back()->withErrors(['msg' => 'Regional Minimum Wage (UMP) is not set or 0.']);

        $employees = $userCompany->employees()
            ->whereHas('attendances', function ($q) use ($start, $end) {
                $q->where('period_start', $start)->where('period_end', $end);
            })
            ->with(['allowEmps.allow', 'deductEmps.deduct', 'attendances' => function ($q) use ($start, $end) {
                $q->where('period_start', $start)->where('period_end', $end);
            }])
            ->get();

        if ($employees->isEmpty()) {
            return back()->withErrors(['msg' => "No attendance data found for period $start to $end."]);
        }

        $bpjsBase = $companyConfig->ump_amount;
        $bpjsKes = min($bpjsBase, $companyConfig->kes_cap_amount);

        $bpjsKesEmp  = $bpjsKes * ($companyConfig->kes_emp_percent / 100);
        $bpjsKesComp = $bpjsKes * ($companyConfig->kes_comp_percent / 100);
        $bpjsJkk = $bpjsBase * ($companyConfig->bpjs_jkk_rate / 100);
        $bpjsJkm = $bpjsBase * ($companyConfig->jkm_comp_percent / 100);
        $bpjsJhtEmp  = $bpjsBase * ($companyConfig->jht_emp_percent / 100);
        $bpjsJhtComp = $bpjsBase * ($companyConfig->jht_comp_percent / 100);
        $baseJp = min($bpjsBase, $companyConfig->jp_cap_amount);
        $bpjsJpEmp  = $baseJp * ($companyConfig->jp_emp_percent / 100);
        $bpjsJpComp = $baseJp * ($companyConfig->jp_comp_percent / 100);

        DB::beginTransaction();

        try {
            $countProcessed = 0;
            $totalExpenseForLog = 0;

            foreach ($employees as $emp) {

                $attendance = $emp->attendances->first();
                $daysPresent = $attendance ? $attendance->total_present : 0;
                $totalLate = $attendance ? $attendance->total_late : 0;
                $totalAlpha = $attendance ? $attendance->total_alpha : 0;
                $totalPermission = $attendance ? $attendance->total_permission : 0;
                $workDays = $emp->working_days;
                $payrollMethod = $emp->payroll_method;

                // === BASE ===
                $baseRate = $emp->base_salary ?? 0;
                $calculatedBaseSalary = 0;

                if ($emp->status == 'DAILY_WORKER') {
                    $calculatedBaseSalary = $baseRate * $daysPresent;
                } else {
                    $calculatedBaseSalary = $baseRate;
                }

                $totalAllowance = 0;
                $totalDeduction = 0;
                $taxableIncomeBase = $calculatedBaseSalary;
                $detailsToSave = [];

                $detailsToSave[] = ['name' => ($emp->status == 'DAILY_WORKER') ? 'Total Upah Harian' : 'Gaji Pokok', 'category' => 'base', 'amount' => $calculatedBaseSalary];

                // === ALLOWANCES ===
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

                    if ($master->is_taxable) $taxableIncomeBase += $amount;

                    $detailsToSave[] = [
                        'name' => $master->name,
                        'category' => 'allowance',
                        'amount' => $amount,
                    ];
                }

                // === BPJS ===
                $bpjsEmpDeduction = 0;
                $companyBenefit = 0;

                $partKes = $emp->participates_bpjs_kes ?? true;
                $partTk  = $emp->participates_bpjs_tk ?? true;
                $partJp  = $emp->participates_bpjs_jp ?? true;

                if ($companyConfig->bpjs_kes_active && $partKes) {
                    $bpjsEmpDeduction += $bpjsKesEmp;
                    $companyBenefit   += $bpjsKesComp;

                    $detailsToSave[] = ['name' => 'BPJS Kes', 'category' => 'deduction', 'amount' => $bpjsKesEmp];
                    $detailsToSave[] = ['name' => 'Tunj. BPJS Kes', 'category' => 'benefit', 'amount' => $bpjsKesComp];
                }

                if ($companyConfig->bpjs_tk_active && $partTk) {
                    $companyBenefit += ($bpjsJkk + $bpjsJkm);
                    $bpjsEmpDeduction += $bpjsJhtEmp;

                    $detailsToSave[] = ['name' => 'JKK', 'category' => 'benefit', 'amount' => $bpjsJkk];
                    $detailsToSave[] = ['name' => 'JKM', 'category' => 'benefit', 'amount' => $bpjsJkm];
                    $detailsToSave[] = ['name' => 'BPJS JHT', 'category' => 'deduction', 'amount' => $bpjsJhtEmp];
                    $detailsToSave[] = ['name' => 'JHT', 'category' => 'benefit', 'amount' => $bpjsJhtComp];
                }

                if ($companyConfig->bpjs_tk_active && $partJp) {
                    $bpjsEmpDeduction += $bpjsJpEmp;

                    $detailsToSave[] = ['name' => 'BPJS JP', 'category' => 'deduction', 'amount' => $bpjsJpEmp];
                    $detailsToSave[] = ['name' => 'JP', 'category' => 'benefit', 'amount' => $bpjsJpComp];
                }

                $totalDeduction += $bpjsEmpDeduction;

                // === PPH 21 (TER) ===
                $ptkpStatus = $emp->ptkp_status ?? 'TK/0';
                $ptkpRule = GlobalPtkp::where('code', $ptkpStatus)->first();

                if ($ptkpRule) {
                    $taxMethod = $companyConfig->tax_method ?? 'GROSS';
                    $brutoDasar = $taxableIncomeBase + $companyBenefit;

                    if ($taxMethod == 'GROSS') {
                        $pph21 = $this->calculatePph21TER($brutoDasar, $ptkpRule->ter_category);
                        if ($pph21 > 0) {
                            $totalDeduction += $pph21;
                            $detailsToSave[] = ['name' => 'PPh 21 (Gross)', 'category' => 'deduction', 'amount' => $pph21];
                        }
                    } elseif ($taxMethod == 'NET') {
                        $pph21 = $this->calculatePph21TER($brutoDasar, $ptkpRule->ter_category);
                        if ($pph21 > 0) {
                            $detailsToSave[] = ['name' => 'PPh 21 (Ditanggung Perusahaan)', 'category' => 'deduction', 'amount' => 0,];
                        }
                    } elseif ($taxMethod == 'GROSS UP') {
                        $tunjanganPajak = 0;
                        $iterasiBruto = $brutoDasar;
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

                            $detailsToSave[] = ['name' => 'Tunjangan PPh 21', 'category' => 'benefit', 'amount' => $tunjanganPajak];
                            $detailsToSave[] = ['name' => 'Potongan PPh 21', 'category' => 'deduction', 'amount' => $tunjanganPajak];
                        }
                    } elseif ($taxMethod == 'GROSS_UP') {
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
                            $detailsToSave[] = ['name' => 'Tunjangan PPh 21', 'category' => 'benefit', 'amount' => $tunjanganPajak];
                            $detailsToSave[] = ['name' => 'Potongan PPh 21', 'category' => 'deduction', 'amount' => $tunjanganPajak];
                        }
                    }
                }

                // ===  POTONGAN MANUAL ===
                foreach ($emp->deductEmps as $assign) {
                    $master = $assign->deduct;
                    $amount = $assign->amount;
                    $totalDeduction += $amount;
                    $detailsToSave[] = ['name' => $master->name, 'category' => 'deduction', 'amount' => $amount];
                }

                // === PENALTI ABSENSI ===
                $dailySalary = ($emp->status == 'DAILY_WORKER') ? $baseRate : ($baseRate / $workDays);

                if ($emp->status != 'DAILY_WORKER') {
                    if ($totalAlpha > 0) {
                        $alphaPenalty = $totalAlpha * $dailySalary;
                        $totalDeduction += $alphaPenalty;
                        $detailsToSave[] = [
                            'name' => 'Alpha (' . $totalAlpha . ')',
                            'category' => 'deduction',
                            'amount' => $alphaPenalty,
                        ];
                    }

                    if ($totalPermission > 0) {
                        $permissionPenalty = $totalPermission * ($dailySalary * 0.5);
                        $totalDeduction += $permissionPenalty;
                        $detailsToSave[] = [
                            'name' => 'Izin (' . $totalPermission . ')',
                            'category' => 'deduction',
                            'amount' => $permissionPenalty,
                        ];
                    }
                }
                
                if ($totalLate > 0) {
                    $latePenalty = $totalLate * 27300;
                    $totalDeduction += $latePenalty;
                    $detailsToSave[] = [
                        'name' => 'Terlambat (' . $totalLate . ')',
                        'category' => 'deduction',
                        'amount' => $latePenalty,
                    ];
                }
                // === INFAQ ===
                $currentNet = $calculatedBaseSalary + $totalAllowance - $totalDeduction;

                if ($companyConfig->infaq_percent > 0) {
                    $infaqAmount = $currentNet * ($companyConfig->infaq_percent / 100);

                    if ($infaqAmount > 0) {
                        $totalDeduction += $infaqAmount;
                        $detailsToSave[] = [
                            'name' => 'Infaq (' . $companyConfig->infaq_percent . '%)',
                            'category' => 'deduction',
                            'amount' => $infaqAmount
                        ];
                    }
                }

                // === F. FINALISASI ===
                $netSalary = $calculatedBaseSalary + $totalAllowance - $totalDeduction;
                $totalExpenseForLog += $netSalary;

                $payroll = Payroll::create([
                    'compani_id' => $userCompany->id,
                    'employee_id' => $emp->id,
                    'pay_period_start' => $start,
                    'pay_period_end' => $end,
                    'base_salary' => $calculatedBaseSalary,
                    'total_allowances' => $totalAllowance,
                    'total_deductions' => $totalDeduction,
                    'net_salary' => $netSalary,
                    'status' => 'pending',
                    'working_days' => $workDays,
                    'payroll_method' => $payrollMethod,
                ]);

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

            // === LOG ===
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

        Cache::forget('payroll_' . $userCompany->id . '_page_1');
        Cache::forget('payroll_period_' . $userCompany->id . '_' . $start . '_' . $end);

        return redirect()->route('payroll')->with('success', "Deleted payroll batch ($deleted records).");
    }

    public function destroy($id)
    {
        $userCompany = Auth::user()->compani;
        $payroll = $userCompany->payrolls()->with('employee')->findOrFail($id);

        $start = $payroll->pay_period_start;
        $end = $payroll->pay_period_end;
        $employeeName = $payroll->employee->name;

        $payroll->delete();

        $this->logActivity('Delete Payroll Slip', "Menghapus slip gaji milik {$employeeName} untuk periode {$start}", $userCompany->id);

        Cache::forget('payroll_' . $userCompany->id . '_page_1');
        Cache::forget('payroll_detail_' . $id);
        Cache::forget('payroll_period_' . $userCompany->id . '_' . $start . '_' . $end);

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

    public function exportReport(Request $request)
    {
        $userCompany = Auth::user()->compani;
        
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date',
        ]);

        $exists = $userCompany->payrolls()
            ->where('pay_period_start', $request->start)
            ->where('pay_period_end', $request->end)
            ->exists();

        if (!$exists) {
            return redirect()->route('payroll')->withErrors(['msg' => 'No data found for report.']);
        }
        
        $this->logActivity('Export Payroll Report', "Mengunduh Laporan Analisa Gaji periode {$request->start}", $userCompany->id);

        $filename = 'Laporan_Gaji_' . $request->start . '.xlsx';
        
        return Excel::download(new PayrollReportExport($userCompany->id, $request->start, $request->end), $filename);
    }
}