<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Outlet;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OutletController extends Controller
{
    public function index($branchId)
    {
        if (! Auth::check()) {
            return redirect('/');
        }

        $userCompany = Auth::user()->compani;

        if (! $userCompany) {
            return redirect()->route('addcompany');
        }

        $status = $userCompany->status;

        if ($status !== 'Settlement') {
            return redirect()->route('login');
        }

        $branch = Branch::where('id', $branchId)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $cacheKey = "outlets_branch_{$branchId}";

        $outlets = Cache::remember($cacheKey, 180, function () use ($branch) {
            return $branch->outlets()->orderBy('name')->get();
        });

        return view('outlet', compact('outlets', 'branch'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        Outlet::create($request->all());

        $this->logActivity('Create Outlet', "Menambah outlet: {$request->name}", $userCompany->id);
        $this->clearCache($request->branch_id);

        return redirect()->route('outlet', ['branchId' => $request->branch_id])->with('success', 'Outlet created successfully');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;
        
        $outlet = Outlet::where('id', $id)->firstOrFail();

        if($outlet->branch->compani_id != $userCompany->id) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $outlet->update($request->only(['name', 'phone', 'address']));

        $this->logActivity('Update Outlet', "Mengubah outlet: {$outlet->name}", $userCompany->id);
        $this->clearCache($outlet->branch_id);

        return redirect()->back()->with('success', 'Outlet updated');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;
        $outlet = Outlet::findOrFail($id);
        
        if($outlet->branch->compani_id != $userCompany->id) abort(403);

        $name = $outlet->name;
        $branchId = $outlet->branch_id;

        $outlet->delete();

        $this->logActivity('Delete Outlet', "Menghapus outlet: {$name}", $userCompany->id);
        $this->clearCache($branchId);

        return redirect()->back()->with('success', 'Outlet deleted');
    }

    public function getByBranch($branchId)
    {
        $outlets = Outlet::where('branch_id', $branchId)->select('id', 'name')->get();
        return response()->json($outlets);
    }
    
    private function logActivity($type, $description, $companyId)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'compani_id' => $companyId,
            'activity_type' => $type,
            'description' => $description,
            'created_at' => now(),
        ]);

        Cache::forget("activities_{$companyId}");
    }

    private function clearCache($branchId) {
        Cache::forget("outlets_branch_{$branchId}");
    }
}