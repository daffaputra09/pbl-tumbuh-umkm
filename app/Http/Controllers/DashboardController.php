<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard for village officers and the village head.
     */
    public function __invoke(Request $request, DashboardService $dashboard): View
    {
        $user = $request->user();
        $accountRole = $user instanceof User ? $user->role : User::ROLE_OFFICER;
        $viewRole = $accountRole === User::ROLE_VILLAGE_HEAD
            ? DashboardService::ROLE_KEPALA_DESA
            : DashboardService::ROLE_PETUGAS;

        return view('dashboard.index', [
            'dashboard' => [
                ...$dashboard->build($viewRole),
                'accountRole' => $accountRole,
            ],
        ]);
    }
}
