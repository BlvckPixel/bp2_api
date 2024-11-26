<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Stripe\PaymentIntent;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Package;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        // Validate request data
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $user = User::where('uuid', $request->user_id)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $payment = $user->payments()->where('status', 'pending')->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending payment found',
                ], Response::HTTP_NOT_FOUND);
            }

            // convert amount to to string
            $amount = round($payment->amount, 2) * 100;

            Log::channel('stderr')->info('Amount: ' . $amount);


            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'gbp',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => $user->uuid,
                ],
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function createSubscriptionPayment(Request $request)
    {
        // Validate request data
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'payment_method_id' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $user = User::where('uuid', $request->user_id)->first();
            $package = Package::findOrFail($request->package_id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            // Create a Stripe customer if not exists
            if (!$user->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }

            // Retrieve the payment method using the provided payment_method_id
            $paymentMethod = \Stripe\PaymentMethod::retrieve($request->payment_method_id);

            // Attach payment method to customer
            $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

            // Set the default payment method on the customer
            \Stripe\Customer::update(
                $user->stripe_customer_id,
                ['invoice_settings' => ['default_payment_method' => $request->payment_method_id]]
            );

            // Create a subscription
            $subscription = \Stripe\Subscription::create([
                'customer' => $user->stripe_customer_id,
                'items' => [['price' => $package->stripe_price_id]],
            ]);

            return response()->json([
                'success' => true,
                'subscription_id' => $subscription->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
