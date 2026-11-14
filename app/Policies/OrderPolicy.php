<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the order.
     *
     * Admins may update orders that are paid and in either processing or shipped state.
     */
    public function update(User $user, Order $order): bool
    {
        // Admin check – the project provides User::isAdmin()
        if (! $user->isAdmin()) {
            return false;
        }

        // Only allow when the order is paid.
        if ($order->payment_status !== 'paid') {
            return false;
        }

        // Allow processing → shipped and shipped → completed transitions.
        return in_array($order->order_status, ['processing', 'shipped'], true);
    }
}
