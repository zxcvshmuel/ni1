<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\CreditPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditService
{
    /**
     * Add credits to a user's account
     *
     * @param User $user
     * @param int $amount
     * @param string|null $reason
     * @return bool
     */
    public function addCredits(User $user, int $amount, ?string $reason = null): bool
    {
        try {
            DB::beginTransaction();

            // Update user credits
            $user->increment('credits', $amount);

            // Log the credit addition
            activity()
                ->performedOn($user)
                ->withProperties([
                    'amount' => $amount,
                    'reason' => $reason,
                    'previous_balance' => $user->credits - $amount,
                    'new_balance' => $user->credits,
                ])
                ->log('credits_added');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add credits', [
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Remove credits from a user's account
     *
     * @param User $user
     * @param int $amount
     * @param string|null $reason
     * @return bool
     */
    public function removeCredits(User $user, int $amount, ?string $reason = null): bool
    {
        if ($user->credits < $amount) {
            return false;
        }

        try {
            DB::beginTransaction();

            // Update user credits
            $user->decrement('credits', $amount);

            // Log the credit removal
            activity()
                ->performedOn($user)
                ->withProperties([
                    'amount' => $amount,
                    'reason' => $reason,
                    'previous_balance' => $user->credits + $amount,
                    'new_balance' => $user->credits,
                ])
                ->log('credits_removed');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove credits', [
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Process a credit package purchase
     *
     * @param User $user
     * @param CreditPackage $package
     * @param string $paymentId
     * @param string $paymentMethod
     * @return Order|null
     */
    public function processPurchase(
        User $user,
        CreditPackage $package,
        string $paymentId,
        string $paymentMethod
    ): ?Order {
        try {
            DB::beginTransaction();

            // Create order record
            $order = Order::create([
                'user_id' => $user->id,
                'credit_package_id' => $package->id,
                'amount' => $package->price,
                'credits' => $package->credits,
                'status' => 'completed',
                'payment_id' => $paymentId,
                'payment_method' => $paymentMethod,
                'invoice_number' => $this->generateInvoiceNumber(),
            ]);

            // Add credits to user
            $this->addCredits($user, $package->credits, "Purchase - Order #{$order->id}");

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process credit package purchase', [
                'user_id' => $user->id,
                'package_id' => $package->id,
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generate a unique invoice number
     *
     * @return string
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = date('Ym');
        $lastOrder = Order::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->first();

        if ($lastOrder) {
            $sequence = intval(substr($lastOrder->invoice_number, -5)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}