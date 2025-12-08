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

        $cacheKey = 'branches_' . $userCompany->id;

        $branches = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userCompany) {
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

        Branch::create($data);

        $this->logActivity('Create Branch', "Membuat cabang baru {$request->name} ({$request->category})", $userCompany->id);

        Cache::forget('branches_' . $userCompany->id);

        return redirect(route('branch'))->with('success', 'Branch successfully created!');
    }

    public function show($id)
    {
        $userCompany = auth()->user()->compani;

        $branch = Branch::with('employees')
            ->where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'data' => $branch
        ]);
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'category' => 'required',
        ]);

        $branch = Branch::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldData = $branch->only(['name', 'address', 'phone', 'category']);
        $newData = $request->only(['name', 'address', 'phone', 'category']);

        $branch->update($newData);

        $changes = [];
        foreach ($newData as $key => $value) {
            if ($oldData[$key] != $value) {
                $fieldLabel = $fieldLabel = ucfirst(str_replace('_', ' ', $key));;
                $changes[] = "$fieldLabel diubah dari '{$oldData[$key]}' menjadi '{$value}'";
            }
        }

        if (!empty($changes)) {
            $descriptionString = "Update Branch {$branch->name}: " . implode(', ', $changes);
            
            $this->logActivity('Update Branch', $descriptionString, $userCompany->id);
        }

        Cache::forget('branches_' . $userCompany->id);

        return redirect(route('branch'))->with('success', 'Branch successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;
        $branch = Branch::where('id', $id)->where('compani_id', $userCompany->id)->first();

        if ($branch) {
            $name = $branch->name;
            $branch->delete();

            $this->logActivity('Delete Branch', "Menghapus cabang: {$name}", $userCompany->id);

            Cache::forget('branches_' . $userCompany->id);
        }

        return redirect(route('branch'))->with('success', 'Branch successfully deleted!');
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