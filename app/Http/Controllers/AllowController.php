<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Allow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AllowController extends Controller
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

        $cacheKey = 'allowances_' . $userCompany->id;

        $allowances = Cache::remember($cacheKey, 60, function () use ($userCompany) {
            return $userCompany->allows()->get();
        });

        return view('allowance', compact('allowances'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        $data['compani_id'] = $userCompany->id;
        $data['is_taxable'] = $request->has('is_taxable');

        $allow = Allow::create($data);

        $this->logActivity('Create Allowance', "Menambahkan allowance baru: {$allow->name} ({$allow->type})", $userCompany->id);

        Cache::forget('allowances_' . $userCompany->id);

        return redirect(route('allowance'))->with('success', 'Allowance successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        $allow = Allow::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldData = [
            'name' => $allow->name,
            'type' => $allow->type,
            'is_taxable' => $allow->is_taxable ? 'Yes' : 'No'
        ];

        $newData = [
            'name' => $request->name,
            'type' => $request->type,
            'is_taxable' => $request->has('is_taxable') 
        ];

        $allow->update($newData);

        $newData['is_taxable'] = $newData['is_taxable'] ? 'Yes' : 'No';

        $changes = [];
        foreach ($newData as $key => $value) {
            if ($oldData[$key] != $value) {
                $fieldLabel = ucfirst(str_replace('_', ' ', $key));
                $changes[] = "$fieldLabel diubah dari '{$oldData[$key]}' menjadi '{$value}'";
            }
        }

        if (!empty($changes)) {
            $descriptionString = "Update Allowance {$allow->name}: " . implode(', ', $changes);
            $this->logActivity('Update Allowance', $descriptionString, $userCompany->id);
        }

        Cache::forget('allowances_' . $userCompany->id);

        return redirect(route('allowance'))->with('success', 'Allowance successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $allowance = Allow::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        if ($allowance) {
            $name = $allowance->name;
            $allowance->delete();

            $this->logActivity('Delete Allowance', "Menghapus allowance: {$name}", $userCompany->id);
        }

        Cache::forget('allowances_' . $userCompany->id);

        return redirect(route('allowance'))->with('success', 'Allowance successfully deleted!');
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
