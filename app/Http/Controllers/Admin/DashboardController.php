<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_tenants'  => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'total_plans'    => Plan::count(),
            'active_plans'   => Plan::where('is_active', true)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}