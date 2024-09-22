<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\CustomEmail;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Exception;

class EmailController extends Controller
{
    public function sendCustomEmail(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        try {
            // Fetch all users
            $users = User::all();

            // Check if there are any users
            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users found to send emails to.',
                ], Response::HTTP_NOT_FOUND);
            }

            $failedEmails = [];

            // Loop through users and send email
            foreach ($users as $user) {
                try {
                    // Send email to each user
                    Mail::to($user->email)->send(new CustomEmail($user, $request->subject, $request->body));

                    // Check for any mail sending failure
                    if (Mail::failures()) {
                        $failedEmails[] = $user->email;
                    }
                } catch (Exception $e) {
                    // Log and capture failed emails
                    $failedEmails[] = $user->email;
                }
            }

            // Check if any emails failed
            if (count($failedEmails) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some emails failed to send.',
                    'failed_emails' => $failedEmails,
                ], Response::HTTP_PARTIAL_CONTENT);
            }

            return response()->json([
                'success' => true,
                'message' => 'Emails sent successfully to all users.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            // Return general error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending emails: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
