<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PayingController extends Controller
{
    /**
     * Get all payments for a specific user.
     */
    public function index($userId)
    {
        $payments = Payment::where('user_id', $userId)->get();

        return response()->json([
            'success' => true,
            'data' => $payments,
        ], Response::HTTP_OK);
    }

    /**
     * Get a specific payment by ID for a user.
     */
    public function show($userId, $id)
    {
        $payment = Payment::where('user_id', $userId)->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $payment,
        ], Response::HTTP_OK);
    }

    /**
     * Create a new payment for a user.
     */
    public function store(Request $request, $userId)
    {
        // $validated = $request->validate([
        //     'package_id' => 'required|exists:packages,id',
        //     'amount' => 'required|numeric',
        //     'status' => 'required|string',
        //     'paid_at' => 'nullable|date',
        // ]);

        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'amount' => 'required|numeric',
            'status' => 'nullable|string', 
            'paid_at' => 'nullable|date',
        ]);
        
        $validated['status'] = $validated['status'] ?? 'pending';
        $validated['paid_at'] = $validated['paid_at'] ?? now();

        $validated['user_id'] = $userId;

        $payment = Payment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully.',
            'data' => $payment,
        ], Response::HTTP_CREATED);
    }

    /**
     * Update an existing payment for a user.
     */
    public function update(Request $request, $userId, $id)
    {
        $payment = Payment::where('user_id', $userId)->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'package_id' => 'sometimes|exists:packages,id',
            'amount' => 'sometimes|numeric',
            'status' => 'sometimes|string',
            'paid_at' => 'nullable|date',
        ]);

        $payment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully.',
            'data' => $payment,
        ], Response::HTTP_OK);
    }

    /**
     * Delete a payment for a user.
     */
    public function destroy($userId, $id)
    {
        $payment = Payment::where('user_id', $userId)->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully.',
        ], Response::HTTP_OK);
    }
}
