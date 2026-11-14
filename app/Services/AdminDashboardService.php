<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminDashboardService
{
    /**
     * Ambil 6 metrik statistik operasional dashboard sesuai PRD Bab 7.1.
     */
    public function getStatistics(): array
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_orders' => Order::count(),
            'orders_awaiting' => Order::where('order_status', 'awaiting_payment')->count(),
            'orders_paid' => Order::where('payment_status', 'paid')->count(),
        ];
    }
}
