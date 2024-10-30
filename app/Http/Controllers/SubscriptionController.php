<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use App\Models\User;
use App\Models\Package;
use Exception;

class SubscriptionController extends Controller
{
    private $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function subscribe(Request $request)
    {
        $validatedData = $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            if ($user->payment_method !== 'card') {
                return response()->json(['error' => 'Only card payments are supported for subscriptions'], 400);
            }

            $package = Package::findOrFail($validatedData['package_id']);
            $result = $this->subscriptionService->createSubscription($user, $package);
            return response()->json($result);

        } catch (Exception $e) {
            return response()->json(['error' => 'Subscription creation failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function changeSubscription(Request $request)
    {
        $validatedData = $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        try {
            $user = auth()->user();
            $newPackage = Package::findOrFail($validatedData['package_id']);
            $result = $this->subscriptionService->changeSubscription($user, $newPackage);
            return response()->json($result);

        } catch (Exception $e) {
            return response()->json(['error' => 'Subscription change failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function cancelSubscription(Request $request)
    {
        try {
            $user = auth()->user();
            $this->subscriptionService->cancelSubscription($user);
            return response()->json(['message' => 'Subscription cancelled successfully']);

        } catch (Exception $e) {
            return response()->json(['error' => 'Subscription cancellation failed', 'message' => $e->getMessage()], 500);
        }
    }
}
