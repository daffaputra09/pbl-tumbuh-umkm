<?php

namespace App\Http\Controllers;

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
        // Role masih dari query string sampai autentikasi tiga peran terpasang.
        $role = in_array($request->query('peran'), DashboardService::ROLES, true)
            ? $request->query('peran')
            : DashboardService::ROLE_PETUGAS;

        return view('dashboard.index', [
            'dashboard' => $dashboard->build($role),
        ]);
    }
}
