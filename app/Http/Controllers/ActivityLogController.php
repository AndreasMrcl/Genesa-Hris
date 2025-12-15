<?php

namespace App\Http\Controllers;

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

        $cacheKey = "activities_{$userCompany->id}";

        $logs = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return $userCompany->activityLogs()->latest()->get();
        });

        return view('activityLog', compact('logs'));
    }
}
