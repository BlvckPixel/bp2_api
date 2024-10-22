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
}

// two

// app/Http/Controllers/EmailController.php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Mail;

// class EmailController extends Controller
// {
//     // app/Http/Controllers/EmailController.php

//     public function sendCustomEmail(Request $request)
//     {
//         // Validate the request
//         $request->validate([
//             'emailArray' => 'required|array',
//             'emailArray.*' => 'email',
//             'subject' => 'required|string',
//             'content' => 'required|string',
//         ]);

//         // Send emails using a view
//         foreach ($request->emailArray as $email) {
//             Mail::send('emails.customEmail', [
//                 'subject' => $request->subject,
//                 'content' => $request->content,
//             ], function ($message) use ($email, $request) {
//                 $message->to($email)
//                         ->subject($request->subject);
//             });
//         }

//         return response()->json(['message' => 'Emails sent successfully!']);
//     }
// }

// ends here









// app/Http/Controllers/EmailController.php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\CustomEmail; // Ensure you have the correct import for your Mailable
// use App\Models\User; // Import the User model
// use Exception;
// use Illuminate\Http\Response;

// class EmailController extends Controller
// {
//     public function sendCustomEmail(Request $request)
//     {
//         // Validate request data
//         $validated = $request->validate([
//             'emailArray' => 'required|array',
//             'emailArray.*' => 'email',
//             'subject' => 'required|string',
//             'body' => 'required|string',
//         ]);

//         $failedEmails = []; // Array to hold emails that failed to send

//         // Loop through the provided email array and send email
//         foreach ($validated['emailArray'] as $email) {
//             try {
//                 // Fetch the user object based on the email
//                 $user = User::where('email', $email)->first();

//                 // Check if user exists
//                 if ($user) {
//                     // Send email to the user
//                     Mail::to($email)->send(new CustomEmail($user, $validated['subject'], $validated['body']));
//                 } else {
//                     // If user not found, add to failed emails
//                     $failedEmails[] = $email;
//                 }
//             } catch (Exception $e) {
//                 // Log and capture failed emails
//                 $failedEmails[] = $email; // Add to failed emails on exception
//             }
//         }

//         // Check if any emails failed
//         if (count($failedEmails) > 0) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Some emails failed to send.',
//                 'failed_emails' => $failedEmails,
//             ], Response::HTTP_PARTIAL_CONTENT);
//         }

//         return response()->json([
//             'success' => true,
//             'message' => 'Emails sent successfully to all recipients.',
//         ], Response::HTTP_OK);
//     }
// }



















// app/Http/Controllers/EmailController.php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\User; // Assuming you have a User model
// use Illuminate\Support\Facades\Mail;

// class EmailController extends Controller
// {
//     public function sendCustomEmail(Request $request)
//     {
//         // Validate the request
//         $request->validate([
//             'emailArray' => 'required|array',
//             'emailArray.*' => 'email',
//             'subject' => 'required|string',
//             'content' => 'required|string',
//         ]);

//         // Fetch user names from the database
//         $userNames = User::whereIn('email', $request->emailArray)->pluck('name', 'email');

//         // Send emails
//         foreach ($request->emailArray as $email) {
//             if ($userNames->has($email)) {
//                 $name = $userNames[$email];
//                 Mail::raw($request->content, function ($message) use ($email, $name, $request) {
//                     $message->to($email, $name)
//                             ->subject($request->subject); // Use the subject from the request
//                 });
//             }
//         }

//         return response()->json(['message' => 'Emails sent successfully!']);
//     }
// }



// app/Http/Controllers/EmailController.php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Mail;

// class EmailController extends Controller
// {
//     public function sendCustomEmail(Request $request)
//     {
//         // Validate the request
//         $request->validate([
//             'emailArray' => 'required|array',
//             'emailArray.*' => 'email',
//             'subject' => 'required|string',
//             'content' => 'required|string',
//         ]);

//         // Send emails directly without fetching user names
//         foreach ($request->emailArray as $email) {
//             Mail::raw($request->content, function ($message) use ($email, $request) {
//                 $message->to($email) // No name needed
//                         ->subject($request->subject); // Use the subject from the request
//             });
//         }

//         return response()->json(['message' => 'Emails sent successfully!']);
//     }
// }