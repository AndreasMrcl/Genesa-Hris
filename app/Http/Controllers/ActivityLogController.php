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
        
        $status = $userCompany->status;

        if ($status !== 'Settlement') {
            return redirect()->route('login');
        }
        
        $page = request()->get('page', 1);

        $cacheTag = 'activities_' . $userCompany->id;

        $cacheKey = 'page_' . $page;

        $logs = Cache::tags([$cacheTag])->remember($cacheKey, now()->addMinutes(60), function () use ($userCompany) {
            return $userCompany->activityLogs()->with('user')
                ->latest()
                ->paginate(15);
        });

        return view('activityLog', compact('logs'));
    }

}
