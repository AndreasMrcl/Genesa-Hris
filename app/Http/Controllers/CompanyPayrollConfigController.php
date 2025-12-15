<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\CompanyPayrollConfig;
use App\Models\GlobalPtkp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CompanyPayrollConfigController extends Controller
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

        $cacheKey = 'company_payroll_config_' . $userCompany->id;

        $config = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return $userCompany->companyPayrollConfig;
        });

        if (!$config) {
            $config = new CompanyPayrollConfig();
            $config->bpjs_jkk_rate = 0.24;
            $config->tax_method = 'GROSS';
            $config->infaq_percent = 0;
            $config->kes_comp_percent = 4.0;
            $config->kes_emp_percent = 1.0;
            $config->kes_cap_amount = 12000000;
            $config->jht_comp_percent = 3.7;
            $config->jht_emp_percent = 2.0;
            $config->jp_comp_percent = 2.0;
            $config->jp_emp_percent = 1.0;
            $config->jp_cap_amount = 10547400;
            $config->jkm_comp_percent = 0.30;
        }

        $ptkps = GlobalPtkp::orderBy('ter_category')->orderBy('code')->get();

        return view('companyConfig', compact('config', 'ptkps'));
    }

    public function update(Request $request)
    {
        $userCompany = Auth::user()->compani;

        $request->validate([
            'bpjs_jkk_rate'     => 'required|numeric|min:0',
            'tax_method'        => 'required|in:GROSS,NET,GROSS_UP',
            'ump_amount'        => 'required|numeric|min:0',
            'infaq_percent'     => 'nullable|numeric|min:0',
            'kes_comp_percent'  => 'required|numeric|min:0',
            'kes_emp_percent'   => 'required|numeric|min:0',
            'kes_cap_amount'    => 'required|numeric|min:0',
            'jht_comp_percent'  => 'required|numeric|min:0',
            'jht_emp_percent'   => 'required|numeric|min:0',
            'jp_comp_percent'   => 'required|numeric|min:0',
            'jp_emp_percent'    => 'required|numeric|min:0',
            'jp_cap_amount'     => 'required|numeric|min:0',
            'jkm_comp_percent'  => 'required|numeric|min:0',
            'ptkp'              => 'array',
            'ptkp.*.amount'     => 'required|numeric|min:0',
        ]);

        $config = $userCompany->companyPayrollConfig()->updateOrCreate(
            ['compani_id' => $userCompany->id],
            [
                'bpjs_jkk_rate'     => $request->bpjs_jkk_rate,
                'tax_method'        => $request->tax_method,
                'ump_amount'        => $request->ump_amount,
                'infaq_percent'     => $request->infaq_percent ?? 0,
                'bpjs_kes_active'   => $request->has('bpjs_kes_active'),
                'bpjs_tk_active'    => $request->has('bpjs_tk_active'),
                'kes_comp_percent'  => $request->kes_comp_percent,
                'kes_emp_percent'   => $request->kes_emp_percent,
                'kes_cap_amount'    => $request->kes_cap_amount,
                'jht_comp_percent'  => $request->jht_comp_percent,
                'jht_emp_percent'   => $request->jht_emp_percent,
                'jp_comp_percent'   => $request->jp_comp_percent,
                'jp_emp_percent'    => $request->jp_emp_percent,
                'jp_cap_amount'     => $request->jp_cap_amount,
                'jkm_comp_percent'  => $request->jkm_comp_percent,
            ]
        );

        if ($request->has('ptkp')) {
            foreach ($request->ptkp as $id => $data) {
                GlobalPtkp::where('id', $id)->update(['amount' => $data['amount']]);
            }
        }

        $this->logActivity(
            'Update Config',
            'Memperbarui konfigurasi payroll perusahaan',
            $userCompany->id
        );

        Cache::forget('company_payroll_config_' . $userCompany->id);

        return redirect()->back()->with('success', 'Configuration updated successfully!');
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

        Cache::forget("activities_{$companyId}");
    }
}