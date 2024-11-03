<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function updatePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:card,crypto,note', // Validate the payment method
        ]);

        $user = auth()->user(); // Get the authenticated user

        // Update the payment method
        $user->payment_method = $request->payment_method;
        $user->save(); // Save the changes

        return response()->json(['message' => 'Payment method updated successfully']);
    }
}