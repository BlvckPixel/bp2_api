<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Subscription;
use Illuminate\Support\Facades\Validator;
use App\Models\Package;
use Exception;

class AdminSubscriptionController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // Create a new subscription
    public function create(Request $request)
    {

        $request->validate([
            'customer_id' => 'required|string',
            'package_id' => 'required|exists:packages,id',
        ]);

        try {
            $package = Package::findOrFail($request->package_id);

            $subscription = Subscription::create([
                'customer' => $request->customer_id,
                'items' => [['price' => $package->stripe_price_id]],
            ]);

            return response()->json(['subscription' => $subscription], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createSub(Request $request) 
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $validated = $request->validate([
            'customer_id' => 'required|string',
            'package_id' => 'required|exists:packages,id',
        ]);

        try {
            $package = Package::findOrFail($validated['package_id']);

            $subscription = Subscription::create([
                'customer' => $validated['customer_id'],
                'items' => [['price' => $package->stripe_price_id]],
            ]);

            return response()->json(['subscription' => $subscription], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Retrieve a subscription
    public function show($id)
    {
        try {
            $subscription = Subscription::retrieve($id);
            return response()->json(['subscription' => $subscription]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    // Update a subscription
    public function update(Request $request, $id)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        try {
            $package = Package::findOrFail($request->package_id);

            $subscription = Subscription::update($id, [
                'items' => [['price' => $package->stripe_price_id]],
            ]);

            return response()->json(['subscription' => $subscription]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Delete a subscription
    public function destroy($id)
    {
        try {
            $subscription = Subscription::retrieve($id);
            $subscription->cancel();

            return response()->json(['message' => 'Subscription cancelled successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}