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

        $cacheKey = 'deductions_' . $userCompany->id;

        $deductions = Cache::remember($cacheKey, 60, function () use ($userCompany) {
            return $userCompany->deducts()->get();
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

        $data['compani_id'] = $userCompany->id;

        $deduct = Deduct::create($data);

        $this->logActivity('Create Allowance', "Menambahkan deduction baru: {$deduct->name} ({$deduct->type})", $userCompany->id);

        Cache::forget('deductions_' . $userCompany->id);

        return redirect(route('deduction'))->with('success', 'Deduction successfully created!');
    }

    public function update(Request $request,$id)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        $deduct = Deduct::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldData = [
            'name' => $deduct->name,
            'type' => $deduct->type,
        ];

        $newData = [
            'name' => $request->name,
            'type' => $request->type,
        ];

        $deduct->update($newData);

        $changes = [];
        foreach ($newData as $key => $value) {
            if ($oldData[$key] != $value) {
                $fieldLabel = ucfirst(str_replace('_', ' ', $key)); 
                $changes[] = "$fieldLabel diubah dari '{$oldData[$key]}' menjadi '{$value}'";
            }
        }

        if (!empty($changes)) {
            $descriptionString = "Update Deduction {$deduct->name}: " . implode(', ', $changes);
            $this->logActivity('Update Deduction', $descriptionString, $userCompany->id);
        }

        Cache::forget('deductions_' . $userCompany->id);

        return redirect(route('deduction'))->with('success', 'Deduction successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $deduction = Deduct::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        if ($deduction) {
            $name = $deduction->name;
            $deduction->delete();

            $this->logActivity('Delete Deduction', "Menghapus deduction: {$name}", $userCompany->id);
            
            Cache::forget('deductions_' . $userCompany->id);
        }

        Cache::forget('deductions_' . $userCompany->id);

        return redirect(route('deduction'))->with('success', 'Deduction successfully deleted!');
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
