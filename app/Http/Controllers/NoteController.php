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

        $cacheKey = "notes_{$userCompany->id}";

        $notes = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return Note::with('employee')
                ->where('compani_id', $userCompany->id)
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

        $note = Note::create([
            'employee_id'     => $data['employee_id'],
            'note_date'     => $data['note_date'],
            'type'     => $data['type'],
            'content'     => $data['content'],
            'compani_id'  => $userCompany->id,
        ]);

        $this->logActivity(
            'Create Note',
            "Menambahkan catatan ({$note->content}) untuk {$note->employee->name}",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('note'))->with('success', 'Note created successfully!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'note_date'   => 'required|date',
            'type'        => 'required|string',
            'content'     => 'required|string',
        ]);

        $note = Note::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $note->content;

        $note->update([
            'employee_id'     => $data['employee_id'],
            'note_date'     => $data['note_date'],
            'type'     => $data['type'],
            'content'     => $data['content'],
        ]);

        $this->logActivity(
            'Update Note',
            "Mengubah catatan '{$oldContent}' menjadi '{$note->content}' untuk {$note->employee->name}",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('note'))->with('success', 'Note updated successfully!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $note = Note::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->first();

        $oldContent = $note->content;

        $note->delete();

        $this->logActivity(
            'Delete Note',
            "Menghapus catatan '{$oldContent}' untuk {$note->employee->name}",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('note'))->with('success', 'Note deleted successfully!');
    }

    private function clearCache($companyId)
    {
        Cache::forget("notes_{$companyId}");
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
