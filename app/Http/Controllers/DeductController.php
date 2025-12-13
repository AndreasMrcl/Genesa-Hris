<?php

namespace App\Http\Controllers;

use App\Models\Deduct;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DeductController extends Controller
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

        $cacheKey = "deductions_{$userCompany->id}";

        $deductions = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return Deduct::where('compani_id', $userCompany->id)->get();
        });

        return view('deduction', compact('deductions'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        $deduct = Deduct::create([
            'name'     => $data['name'],
            'type'     => $data['type'],
            'compani_id'  => $userCompany->id,
        ]);

        $this->logActivity(
            'Create Allowance',
            "Menambahkan deduction '{$deduct->name}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('deduction'))->with('success', 'Deduction successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        $deduct = Deduct::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $deduct->name;

        $deduct->update([
            'name' => $data['name'],
            'type' => $data['type'],
        ]);

        $this->logActivity(
            'Update Deduction',
            "Mengubah deduction '{$oldContent}' menjadi '{$deduct->name}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('deduction'))->with('success', 'Deduction successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $deduction = Deduct::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $deduction->name;

        $deduction->delete();

        $this->logActivity(
            'Delete Deduction',
            "Menghapus deduction '{$oldContent}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('deduction'))->with('success', 'Deduction successfully deleted!');
    }

    private function clearCache($companyId)
    {
        Cache::forget("deductions_{$companyId}");
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
