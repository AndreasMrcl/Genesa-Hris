<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AnnouncementController extends Controller
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

        $cacheKey = "announcements_{$userCompany->id}";

        $announcements = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return Announcement::where('compani_id', $userCompany->id)
                ->latest('created_at')
                ->get();
        });

        return view('announcement', compact('announcements'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'content'     => 'required|string',
        ]);

        $announcement = Announcement::create([
            'content'     => $data['content'],
            'compani_id'  => $userCompany->id,
        ]);

        $this->logActivity(
            'Create Announcement',
            "Menambahkan announcement '{$announcement->content}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('announcement'))->with('success', 'Ann created successfully!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $announcement = Announcement::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $announcement->content;

        $announcement->update([
            'content' => $data['content'],
        ]);

        $this->logActivity(
            'Update Announcement',
            "Mengubah Announcement '{$oldContent}' menjadi '{$announcement->content}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('announcement'))->with('success', 'Announcement updated successfully!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $announcement = Announcement::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $announcement->content;

        $announcement->delete();

        $this->logActivity(
            'Delete Announcement',
            "Menghapus announcement '{$oldContent}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('announcement'))->with('success', 'Announcement deleted successfully!');
    }

    private function clearCache($companyId)
    {
        Cache::forget("announcements_{$companyId}");
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
