<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ActivityLogController extends Controller
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
        
        $page = request()->get('page', 1);

        $cacheTag = 'activities_' . $userCompany->id;

        $cacheKey = 'page_' . $page;

        $logs = Cache::tags([$cacheTag])->remember($cacheKey, now()->addMinutes(60), function () use ($userCompany) {
            return ActivityLog::with('user')
                ->where('compani_id', $userCompany->id) // Filter hanya log perusahaan ini
                ->latest()
                ->paginate(15);
        });

        return view('activityLog', compact('logs'));
    }

}
