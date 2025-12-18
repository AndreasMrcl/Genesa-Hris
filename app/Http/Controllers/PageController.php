<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function profile()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $userCompany = Auth::user()->compani;

        if (!$userCompany) {
            return redirect()->route('addcompany');
        }

        return view('profil', compact('userCompany'));
    }


    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $company = Auth::user()->compani;

        if (!$company || $company->status !== 'Settlement') {
            return redirect()->route('login');
        }

        $totalEmployees = Employee::where('compani_id', $company->id)->count();
        
        $newEmployeesThisMonth = Employee::where('compani_id', $company->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return view('dashboard', compact(
            'totalEmployees',
            'newEmployeesThisMonth'
        ));
    }

    public function search(Request $request)
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

        $searchTerm = $request->input('search');

        $branchesQuery = Branch::where('compani_id', $userCompany->id);

        $positionsQuery = Position::where('compani_id', $userCompany->id);

        $announcementsQuery = Announcement::where('compani_id', $userCompany->id);

        // Apply search filters
        if (!empty($searchTerm)) {

            // Branches
            $branchesQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('category', 'like', '%' . $searchTerm . '%');
            });

            // Positions
            $positionsQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('category', 'like', '%' . $searchTerm . '%');
            });

            // Announcements
            $announcementsQuery->where(function ($query) use ($searchTerm) {
                $query->where('content', 'like', '%' . $searchTerm . '%');
            });
        }

        $branches = $branchesQuery->get();

        $positions = $positionsQuery->get();

        $announcements = $announcementsQuery->get();

        return view('search', compact('branches', 'positions', 'announcements'));
    }
}
