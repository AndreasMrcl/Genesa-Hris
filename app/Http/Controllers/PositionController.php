<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PositionController extends Controller
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

        $cacheKey = "positions_{$userCompany->id}";

        $positions = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return $userCompany->positions()->orderBy('name')->get();
        });

        return view('position', compact('positions'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'base_salary_default' => 'required|numeric|min:0',
        ]);

        $position = Position::create([
            'name'     => $data['name'],
            'category'     => $data['category'],
            'base_salary_default'     => $data['base_salary_default'],
            'compani_id'  => $userCompany->id,
        ]);

        $this->logActivity(
            'Create Position',
            "Menambahkan Position '{$position->name}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('position'))->with('success', 'Position created successfully!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'base_salary_default' => 'required|numeric|min:0',
        ]);

        $position = Position::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $position->name;

        $position->update([
            'name'     => $data['name'],
            'category'     => $data['category'],
            'base_salary_default'     => $data['base_salary_default'],
        ]);

        $this->logActivity(
            'Update Position',
            "Mengubah Position '{$position->name}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('position'))->with('success', 'Position updated successfully!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $position = Position::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $position->name;

        $position->delete();

        $this->logActivity(
            'Delete Position',
            "Menghapus position {$oldContent}'",
            $userCompany->id
        );

        return redirect(route('position'))->with('success', 'Position deleted successfully!');
    }

    private function clearCache($companyId)
    {
        Cache::forget("positions_{$companyId}");
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
