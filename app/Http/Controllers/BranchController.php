<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BranchController extends Controller
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

        $cacheKey = "branches_{$userCompany->id}";

        $branches = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return $userCompany->branches;
        });

        return view('branch', compact('branches'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'category' => 'required',
        ]);

        $data['compani_id'] = $userCompany->id;

        $branch = Branch::create([
            'name'      => $data['name'],
            'address'   => $data['address'],
            'phone'     => $data['phone'],
            'category'  => $data['category'],
            'compani_id'  => $userCompany->id,
        ]);

        $this->logActivity(
            'Create Branch',
            "Membuat cabang baru '{$branch->name}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('branch'))->with('success', 'Branch successfully created!');
    }

    // public function show($id)
    // {
    //     $userCompany = auth()->user()->compani;

    //     $branch = Branch::with('employees')
    //         ->where('id', $id)
    //         ->where('compani_id', $userCompany->id)
    //         ->firstOrFail();

    //     return response()->json([
    //         'status' => true,
    //         'data' => $branch
    //     ]);
    // }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'category' => 'required',
        ]);

        $branch = Branch::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $branch->name;

        $branch->update([
            'name' => $data['name'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'category' => $data['category'],
        ]);

        $this->logActivity(
            'Update Branch',
            "Mengubah Branch '{$oldContent}' menjadi '{$branch->name}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('branch'))->with('success', 'Branch successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $branch = Branch::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $branch->name;

        $branch->delete();

        $this->logActivity(
            'Delete Branch',
            "Menghapus cabang '{$oldContent}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('branch'))->with('success', 'Branch successfully deleted!');
    }

    private function clearCache($companyId)
    {
        Cache::forget("branches_{$companyId}");
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
