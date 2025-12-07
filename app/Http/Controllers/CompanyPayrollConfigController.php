<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\CompanyPayrollConfig;
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

        $config = Cache::remember($cacheKey, now()->addDay(), function () use ($userCompany) {
            return $userCompany->companyPayrollConfig;
        });

        if (!$config) {
            session()->flash('warning', 'Payroll configuration not found. Please configure tax & insurance settings before running payroll.');

            $config = new CompanyPayrollConfig();
            $config->bpjs_jkk_rate = 0.24;
            $config->tax_method = 'GROSS';
            $config->infaq_percent = 1.25;
        }

        return view('companyConfig', compact('config'));
    }

    public function update(Request $request)
    {
        $userCompany = Auth::user()->compani;

        $request->validate([
            'bpjs_jkk_rate'   => 'required|numeric|min:0',
            'tax_method'      => 'required|in:GROSS,NET,GROSS_UP',
            'ump_amount'      => 'required|numeric|min:0',
            'infaq_percent'   => 'nullable|numeric|min:0',
        ]);

        $config = $userCompany->companyPayrollConfig()->updateOrCreate(
            [],
            [
                'bpjs_jkk_rate'   => $request->bpjs_jkk_rate,
                'tax_method'      => $request->tax_method,
                'ump_amount'      => $request->ump_amount,
                'infaq_percent'   => $request->infaq_percent ?? 0,
                'bpjs_kes_active' => $request->has('bpjs_kes_active'),
                'bpjs_tk_active'  => $request->has('bpjs_tk_active'),
            ]
        );

        $this->logActivity('Update Config', 'Memperbarui konfigurasi payroll perusahaan', $userCompany->id);

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

        Cache::tags(['activities_' . $companyId])->flush();
    }
}