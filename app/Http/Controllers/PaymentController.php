<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        // Validate request data
        $validation = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            // 'currency' => 'required|string',
            // 'token' => 'required|string', // Stripe token from the frontend
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
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
}
