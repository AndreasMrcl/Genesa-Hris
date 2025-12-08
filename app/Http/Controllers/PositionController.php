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

        $cacheKey = 'positions_' . $userCompany->id;

        $positions = Cache::remember($cacheKey, 60, function () use ($userCompany) {
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

        $position = $userCompany->positions()->create($data);

        $this->logActivity('Create Position', "Menambahkan jabatan baru: {$position->name}", $userCompany->id);
        
        Cache::forget('positions_' . $userCompany->id);

        return redirect(route('position'))->with('success', 'Position created successfully!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'base_salary_default' => 'required|numeric|min:0',
        ]);

        $position = $userCompany->positions()->findOrFail($id);

        $oldName = $position->name;
        
        $position->update([
            'name' => $request->name,
            'category' => $request->category,
            'base_salary_default' => $request->base_salary_default,
        ]);

        $this->logActivity('Update Position', "Mengubah jabatan {$oldName} menjadi {$position->name}", $userCompany->id);
        
        Cache::forget('positions_' . $userCompany->id);

        return redirect(route('position'))->with('success', 'Position updated successfully!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $position = $userCompany->positions()->find($id);

        if ($position) {
            $name = $position->name;
            $position->delete();

            $this->logActivity('Delete Position', "Menghapus jabatan: {$name}", $userCompany->id);
            
            Cache::forget('positions_' . $userCompany->id);
        }

        return redirect(route('position'))->with('success', 'Position deleted successfully!');
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