<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ShiftController extends Controller
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

        $cacheKey = "shifts_{$userCompany->id}";

        $shifts = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return $userCompany->shifts()
                ->with('branch')
                ->orderBy('branch_id')
                ->orderBy('start_time')
                ->get();
        });

        $branches = $userCompany->branches()->select('id', 'name')->get();

        return view('shift', compact('shifts', 'branches'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'name'       => 'required|string|max:50',
            'branch_id'  => 'nullable|exists:branches,id',
            'start_time' => 'required',
            'end_time'   => 'required',
            'color'      => 'nullable|string',
        ]);

        $data['compani_id'] = $userCompany->id;
        $data['is_cross_day'] = $request->has('is_cross_day');

        $shift = Shift::create($data);
        
        $this->logActivity('Create Master Shift', "Membuat shift baru: {$shift->name} ({$shift->start_time} - {$shift->end_time})", $userCompany->id);

        $this->clearCache($userCompany->id);

        return redirect(route('shift'))->with('success', 'Shift successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'name'       => 'required|string|max:50',
            'branch_id'  => 'nullable|exists:branches,id',
            'start_time' => 'required',
            'end_time'   => 'required',
            'color'      => 'nullable|string',
        ]);

        $shift = Shift::where('id', $id)->where('compani_id', $userCompany->id)->firstOrFail();

        $shift->update([
            'name'         => $request->name,
            'branch_id'    => $request->branch_id,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
            'is_cross_day' => $request->has('is_cross_day'),
            'color'        => $request->color,
        ]);

        $this->logActivity('Update Master Shift', "Mengubah shift: {$shift->name}", $userCompany->id);
        $this->clearCache($userCompany->id);

        return redirect(route('shift'))->with('success', 'Shift successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $shift = Shift::where('id', $id)->where('compani_id', $userCompany->id)->first();

        $shift->delete();

        $this->logActivity('Delete Master Shift', "Menghapus shift: {$shift->name}", $userCompany->id);
        $this->clearCache($userCompany->id);

        return redirect(route('shift'))->with('success', 'Shiift successfully deleted!');
    }

    private function clearCache($companyId)
    {
        Cache::forget("shifts_{$companyId}");
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