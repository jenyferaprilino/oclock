<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(AdminDashboardService $dashboardService): View
    {
        $stats = $dashboardService->getStatistics();

        return view('admin.dashboard', compact('stats'));
    }
}
