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

        if ($userCompany->status !== 'Settlement') {
            return redirect()->route('login');
        }

        $cacheKey = "allowances_{$userCompany->id}";

        $allowances = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return Allow::where('compani_id', $userCompany->id)->get();
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

        $data['is_taxable'] = $request->boolean('is_taxable');

        $allow = Allow::create($data);

        $this->logActivity(
            'Create Allowance',
            "Menambahkan allowance baru: {$allow->name} ({$allow->type})",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect()->route('allowance')->with('success', 'Allowance successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'name' => 'required',
            'type'  => 'required',
        ]);

        $allow = Allow::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $old = [
            'name'       => $allow->name,
            'type'       => $allow->type,
            'is_taxable' => $allow->is_taxable ? 'Yes' : 'No'
        ];

        $new = [
            'name'       => $request->name,
            'type'       => $request->type,
            'is_taxable' => $request->boolean('is_taxable'),
        ];

        $allow->update($new);

        $new['is_taxable'] = $new['is_taxable'] ? 'Yes' : 'No';

        // Detect what changed
        $changes = [];
        foreach ($new as $field => $value) {
            if ($old[$field] != $value) {
                $label = ucfirst(str_replace('_', ' ', $field));
                $changes[] = "{$label} diubah dari '{$old[$field]}' menjadi '{$value}'";
            }
        }

        if ($changes) {
            $desc = "Update Allowance '{$allow->name}': " . implode(', ', $changes);
            $this->logActivity('Update Allowance', $desc, $userCompany->id);
        }

        $this->clearCache($userCompany->id);

        return redirect()->route('allowance')->with('success', 'Allowance successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $allowance = Allow::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $name = $allowance->name;

        $allowance->delete();

        $this->logActivity(
            'Delete Allowance',
            "Menghapus allowance: {$name}",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect()->route('allowance')->with('success', 'Allowance successfully deleted!');
    }

    private function clearCache($companyId)
    {
        Cache::forget("allowances_{$companyId}");
    }

    private function logActivity($type, $description, $companyId)
    {
        ActivityLog::create([
            'user_id'       => auth()->id(),
            'compani_id'    => $companyId,
            'activity_type' => $type,
            'description'   => $description,
        ]);

        Cache::forget("activities_{$companyId}");
    }
}
