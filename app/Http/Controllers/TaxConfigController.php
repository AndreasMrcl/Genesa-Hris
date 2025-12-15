<?php

namespace App\Http\Controllers;

use App\Models\GlobalPtkp;
use App\Models\GlobalTerRate;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TaxConfigController extends Controller
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

        $cacheKeyPtkp = 'global_ptkps_' . $userCompany->id;
        $cacheKeyTerRates = 'global_ter_rates_' . $userCompany->id;

        $ptkps = Cache::remember($cacheKeyPtkp, 180, function () use ($userCompany) {
            return $userCompany->globalPtkps()
                ->orderBy('ter_category')
                ->orderBy('code')
                ->get();
        });

        $allTerRates = Cache::remember($cacheKeyTerRates, 180, function () use ($userCompany) {
            return $userCompany->globalTerRates()
                ->orderBy('gross_income_min')
                ->get();
        });


        $terA = $allTerRates->where('ter_category', 'A');
        $terB = $allTerRates->where('ter_category', 'B');
        $terC = $allTerRates->where('ter_category', 'C');

        return view('taxConfig', compact('ptkps', 'terA', 'terB', 'terC'));
    }

    public function storePtkp(Request $request)
    {
        $userCompany = Auth::user()->compani;
        
        $request->validate([
            'code' => 'required|string|unique:global_ptkps,code',
            'amount' => 'required|numeric|min:0',
            'ter_category' => 'required|in:A,B,C',
        ]);

        $ptkp = GlobalPtkp::create([
            'compani_id' => $userCompany->id,
            'code' => $request->code,
            'amount' => $request->amount,
            'ter_category' => $request->ter_category,
        ]);

        Cache::forget('global_ptkps_' . $userCompany->id);

        $this->logActivity('Create PTKP', "Menambah status PTKP: {$ptkp->code}", $userCompany->id);
        
        return redirect()->back()->with('success', 'PTKP Status added')->with('active_tab', 'ptkp');
    }

    public function updatePtkp(Request $request, $id)
    {
        $userCompany = Auth::user()->compani;

        $ptkp = GlobalPtkp::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();
        
        $request->validate([
            'code' => 'required|string|unique:global_ptkps,code,' . $id,
            'amount' => 'required|numeric|min:0',
            'ter_category' => 'required|in:A,B,C',
        ]);

        $ptkp->update([
            'code' => $request->code,
            'amount' => $request->amount,
            'ter_category' => $request->ter_category,
        ]);

        Cache::forget('global_ptkps_' . $userCompany->id);

        $this->logActivity('Update PTKP', "Mengubah status PTKP: {$ptkp->code}", $userCompany->id);

        return redirect()->back()->with('success', 'PTKP Status updated')->with('active_tab', 'ptkp');
    }

    public function destroyPtkp($id)
    {
        $userCompany = Auth::user()->compani;

        $ptkp = GlobalPtkp::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->first();

        $code = $ptkp->code;

        $ptkp->delete();

        Cache::forget('global_ptkps_' . $userCompany->id);

        $this->logActivity('Delete PTKP', "Menghapus status PTKP: {$code}", $userCompany->id);
        return redirect()->back()->with('success', 'PTKP Status deleted')->with('active_tab', 'ptkp');
    }

    public function storeTer(Request $request)
    {
        $userCompany = Auth::user()->compani;

        $request->validate([
            'ter_category' => 'required|in:A,B,C',
            'gross_income_min' => 'required|numeric|min:0',
            'gross_income_max' => 'nullable|numeric|gt:gross_income_min',
            'rate_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $ter = GlobalTerRate::create([
            'compani_id' => $userCompany->id,
            'ter_category' => $request->ter_category,
            'gross_income_min' => $request->gross_income_min,
            'gross_income_max' => $request->gross_income_max,
            'rate_percentage' => $request->rate_percentage,
        ]);

        Cache::forget('global_ter_rates_' . $userCompany->id);

        $this->logActivity('Create TER', "Menambah tarif TER Kategori {$ter->ter_category}", $userCompany->id);

        return redirect()->back()->with('success', 'TER Rate added')->with('active_tab', 'ter' . $request->ter_category);
    }

    public function updateTer(Request $request, $id)
    {
        $userCompany = Auth::user()->compani;

        $ter = GlobalTerRate::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $request->validate([
            'gross_income_min' => 'required|numeric|min:0',
            'gross_income_max' => 'nullable|numeric|gt:gross_income_min',
            'rate_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $ter->update([
            'gross_income_min' => $request->gross_income_min,
            'gross_income_max' => $request->gross_income_max,
            'rate_percentage' => $request->rate_percentage,
        ]);

        $category = $request->ter_category ?? $ter->ter_category;

        Cache::forget('global_ter_rates_' . $userCompany->id);

        $this->logActivity('Update TER', "Mengubah tarif TER ID #{$id}", $userCompany->id);

        return redirect()->back()->with('success', 'TER Rate updated')->with('active_tab', 'ter' . $category);
    }

    public function destroyTer($id)
    {
        $userCompany = Auth::user()->compani;

        $ter = GlobalTerRate::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->first();
            
        $category = $ter->ter_category;
        $ter->delete();

        Cache::forget('global_ter_rates_' . $userCompany->id);

        $this->logActivity('Delete TER', "Menghapus tarif TER ID #{$id}", $userCompany->id);
        return redirect()->back()->with('success', 'TER Rate deleted')->with('active_tab', 'ter' . $category);
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