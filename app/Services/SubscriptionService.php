<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\PaymentIntent;
use App\Models\User;
use App\Models\Package;
use App\Models\Payment;
use Carbon\Carbon;
use Exception;

class SubscriptionService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createSubscription(User $user, Package $package)
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'status' => 'pending',
            'payment_type' => 'subscription_initial',
            'currency' => 'GBP'
        ]);

        try {
            // Check or create Stripe customer
            if (!$user->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'metadata' => ['user_id' => $user->id]
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            }

            // Create subscription
            $subscription = Subscription::create([
                'customer' => $user->stripe_customer_id,
                'items' => [['price' => $package->stripe_price_id]],
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            $payment->update(['stripe_payment_id' => $subscription->latest_invoice->payment_intent->id]);

            return [
                'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret,
                'subscriptionId' => $subscription->id
            ];
        } catch (Exception $e) {
            $payment->update(['status' => 'failed']);
            throw new Exception("Error creating subscription: " . $e->getMessage());
        }
    }

    public function changeSubscription(User $user, Package $newPackage)
    {
        try {
            $currentSubscription = Subscription::retrieve($user->stripe_subscription_id);
            $currentPackage = Package::where('stripe_price_id', $currentSubscription->items->data[0]->price->id)->first();

            $currentPeriodEnd = Carbon::createFromTimestamp($currentSubscription->current_period_end);
            $daysRemaining = now()->diffInDays($currentPeriodEnd);
            $daysInMonth = now()->daysInMonth;
            $remainingValue = ($currentPackage->price * $daysRemaining) / $daysInMonth;

            $amountToPay = $newPackage->price > $currentPackage->price
                ? $newPackage->price - $remainingValue
                : ($newPackage->price - $remainingValue) + 10;

            $payment = Payment::create([
                'user_id' => $user->id,
                'package_id' => $newPackage->id,
                'amount' => $amountToPay,
                'status' => 'pending',
                'payment_type' => 'subscription_change',
                'currency' => 'GBP'
            ]);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountToPay * 100,
                'currency' => 'gbp',
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
                'setup_future_usage' => 'off_session',
            ]);

            $payment->update(['stripe_payment_id' => $paymentIntent->id]);

            return [
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $amountToPay
            ];
        } catch (Exception $e) {
            throw new Exception("Error changing subscription: " . $e->getMessage());
        }
    }

    public function cancelSubscription(User $user)
    {
        try {
            $subscription = Subscription::retrieve($user->stripe_subscription_id);
            $subscription->cancel();
            $user->update(['stripe_subscription_id' => null]);

            return "Subscription cancelled successfully.";
        } catch (Exception $e) {
            throw new Exception("Error cancelling subscription: " . $e->getMessage());
        }
    }
}
