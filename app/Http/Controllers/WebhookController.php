<?php

namespace App\Http\Controllers;

use App\Mail\TestMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{
    public function __construct()
    {
        $secret = config('services.stripe.secret');
        \Stripe\Stripe::setApiKey($secret);
    }

    public function handleStripeWebhook(Request $request)
    {
        Log::channel('stderr')->info('Webhook received');
        // \Stripe\Stripe::setApiKey('services.stripe.secret');

        $payload = @file_get_contents('php://input');
        $event = null;

        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            default:
                Log::info('Unhandled event type: ' . $event->type);
                // Unexpected event type
                http_response_code(400);
                exit();
        }

        http_response_code(200);
    }

    public function test()
    {
        Log::channel('stderr')->info('Test route hit');
        Mail::to('roqeebyusuff17@gmail.com')->send(new TestMailer());
    }

    public function handleCreateStripeWebhookEndpoint(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'url' => 'required|url',
            'enabled_events' => 'required|array',
            'enabled_events.*' => 'string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first(),
            ], 400);
        }

        $endpoint = \Stripe\WebhookEndpoint::create([
            'url' => $request->url,
            'enabled_events' => $request->enabled_events,
        ]);

        Log::channel('stderr')->info('Webhook endpoint created: ' . $endpoint);

        return response()->json([
            'success' => true,
            'webhook_endpoint' => $endpoint,
        ]);
    }

    public function listStripeWebhookEndpoints()
    {
        $endpoints = \Stripe\WebhookEndpoint::all();

        return response()->json([
            'success' => true,
            'webhook_endpoints' => $endpoints,
        ]);
    }

    // delete stripe webhook endpoint using params id
    public function deleteStripeWebhookEndpoint($id)
    {
        $endpoint = \Stripe\WebhookEndpoint::retrieve($id);
        $temp = $endpoint;
        Log::channel('stderr')->info('Deleting webhook endpoint: ' . $endpoint);

        $endpoint->delete();

        return response()->json([
            'success' => true,
            'message' => 'Webhook endpoint deleted',
            'webhook_endpoint' => $temp,
        ]);
    }

    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        if ($paymentIntent->status !== 'succeeded') {
            Log::channel('stderr')->info('PaymentIntent not successful');
            return;
        }

        Log::channel('stderr')->info('PaymentIntent was successful! ' . $paymentIntent);

        $user = User::where('uuid', $paymentIntent->metadata->user_id)->first();
        if (!$user) {
            Log::channel('stderr')->info('User not found');
            return;
        }

        // find user payment where status is pending
        $payment = $user->payments()->where('status', 'pending')->first();

        if (!$payment) {
            Log::channel('stderr')->info('Payment not found');
            return;
        }

        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        Log::channel('stderr')->info('Payment updated');

        // send email to user
    }
}
