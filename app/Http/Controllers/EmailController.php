<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\CustomEmail;
use App\Mail\AllTemplateEmail;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\EmailTemplate;
use Exception;

class EmailController extends Controller
{
    public function sendCustomEmail(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'emailArray' => 'required|array',
            'emailArray.*' => 'email',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        $failedEmails = []; // Array to hold emails that failed to send

        // Loop through the provided email array and send email
        foreach ($validated['emailArray'] as $email) {
            try {
                // Send email to each recipient
                Mail::to($email)->send(new CustomEmail($email, $validated['subject'], $validated['body']));

                // Check for any mail sending failure
                if (Mail::failures()) {
                    $failedEmails[] = $email; // Add to failed emails if there are failures
                }
            } catch (Exception $e) {
                // Log and capture failed emails
                print($e);
                $failedEmails[] = $email; // Add to failed emails on exception
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
            'message' => 'Emails sent successfully to all recipients.',
        ], Response::HTTP_OK);
    }

    public function sendAuthenticationEmail(Request $request)
    {
        $validated = $request->validate([
            'emailArray' => 'required|array',
            'emailArray.*' => 'email',
        ]);
    
        // Fetch the email template named 'Activation Email'
        $emailTemplate = EmailTemplate::whereRaw('LOWER(name) = ?', ['activation email'])->first();
    
        if (!$emailTemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Activation Email template not found.',
            ], Response::HTTP_NOT_FOUND);
        }
    
        $failedEmails = []; // Array to hold emails that failed to send
    
        foreach ($validated['emailArray'] as $email) {
            $user = User::where('email', $email)->first();
    
            if (!$user) {
                $failedEmails[] = [
                    'email' => $email,
                    'error' => 'User not found.'
                ];
                continue;
            }
    
            $customizedBody = $this->customizeEmailBody($emailTemplate, $user);
    
            try {
                // Queue the email sending process
                Mail::to($email)->queue(new AllTemplateEmail($user->name, $emailTemplate->name, $customizedBody));
            } catch (\Exception $e) {
                $failedEmails[] = [
                    'email' => $email,
                    'error' => $e->getMessage()
                ];
            }
        }
    
        if (count($failedEmails) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Some authentication emails failed to send.',
                'failed_emails' => $failedEmails,
            ], Response::HTTP_PARTIAL_CONTENT);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Authentication emails sent successfully to all recipients.',
        ], Response::HTTP_OK);
    }
    
    private function customizeEmailBody($emailTemplate, $user)
    {
        return str_replace(
            ['{{ $user->name }}', '{{ $user->email }}', '{{ $token }}'],
            [$user->name, $user->email, $user->activation_token],
            $emailTemplate->body
        );
    }
    
}