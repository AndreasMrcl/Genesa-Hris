<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Employee;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NoteController extends Controller
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

        $cacheKey = 'notes_' . $userCompany->id;

        $notes = Cache::remember($cacheKey, 60, function () use ($userCompany) {
            return $userCompany->notes()
                ->with('employee')
                ->latest('created_at') 
                ->get();
        });

        $employee = Employee::where('compani_id', $userCompany->id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('note', compact('notes', 'employee'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'note_date'   => 'required|date',
            'type'        => 'required|string',
            'content'     => 'required|string',
        ]);

        $employee = Employee::where('id', $request->employee_id)
            ->where('compani_id', $userCompany->id)
            ->first();

        if (!$employee) {
            return back()->withErrors(['msg' => 'Employee not found or access denied.']);
        }

        $data['compani_id'] = $userCompany->id;

        $data['status'] = $request->status ?? 'active'; 

        Note::create($data);
        
        $this->logActivity(
            'Create Note', 
            "Menambahkan catatan ({$request->type}) untuk {$employee->name}", 
            $userCompany->id
        );

        Cache::forget('notes_' . $userCompany->id);

        return redirect(route('note'))->with('success', 'Note created successfully!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'note_date'   => 'required|date',
            'type'        => 'required|string',
            'content'     => 'required|string',
        ]);

        $note = Note::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        if ($request->employee_id != $note->employee_id) {
             $validEmployee = Employee::where('id', $request->employee_id)
                ->where('compani_id', $userCompany->id)
                ->exists();
             if (!$validEmployee) abort(403);
        }

        $note->update([
            'employee_id' => $request->employee_id,
            'note_date'   => $request->note_date,
            'type'        => $request->type,
            'content'     => $request->content,
        ]);

        $empName = $note->employee->name ?? 'Unknown';

        $this->logActivity(
            'Update Note', 
            "Mengubah catatan ID #{$note->id} milik {$empName}", 
            $userCompany->id
        );

        Cache::forget('notes_' . $userCompany->id);

        return redirect(route('note'))->with('success', 'Note updated successfully!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $note = Note::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->first();

        if ($note) {
            $empName = $note->employee->name ?? '-';
            $note->delete();

            $this->logActivity(
                'Delete Note', 
                "Menghapus catatan milik {$empName}", 
                $userCompany->id
            );

            Cache::forget('notes_' . $userCompany->id);
        }

        return redirect(route('note'))->with('success', 'Note deleted successfully!');
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
