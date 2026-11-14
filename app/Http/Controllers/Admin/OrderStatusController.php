<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderStatusController extends Controller
{
    /**
     * Update order shipping info and transition status.
     */
    public function update(Request $request, Order $order)
    {
        // Authorization handled by policy (can:update,order) via route middleware.
        $data = $request->validate([
            'courier_name' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'action' => 'required|in:process,complete',
        ]);

        return DB::transaction(function () use ($order, $data) {
            // Update shipping info if provided
            if (isset($data['courier_name'])) {
                $order->courier_name = $data['courier_name'];
            }
            if (isset($data['tracking_number'])) {
                $order->tracking_number = $data['tracking_number'];
            }

            // Determine state transition based on action
            if ($data['action'] === 'process') {
                // Only allow processing when payment is paid and status is processing
                if ($order->payment_status !== 'paid' || $order->order_status !== 'processing') {
                    return redirect()->back()->with('error', 'Order cannot be processed at this stage.');
                }
                if (empty($order->tracking_number)) {
                $order->tracking_number = 'OC-'.Str::upper(Str::random(8));
            }
            $order->order_status = 'shipped';
            } elseif ($data['action'] === 'complete') {
                // Only allow completion when already shipped
                if ($order->order_status !== 'shipped') {
                    return redirect()->back()->with('error', 'Order must be shipped before completing.');
                }
                $order->order_status = 'completed';
            }

            $order->save();

            return redirect()->back()->with('success', 'Order status updated successfully.');
        });
    }
}
