<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use App\Models\User;
use Illuminate\Support\Str;
// use Illuminate\Foundation\Auth\User;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivationMail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use App\Mail\PasswordResetMail;
use App\Models\Package;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validation = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => 'Validation failed', 'errors' => $validation->errors()->toArray()], 400);
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user instanceof User) {
                return response()->json(['error' => 'User not found'], 404);
            }

            if (!$user->is_active) {
                return response()->json(['error' => 'Account not activated'], 403);
            }

            $user->load('role');

            if (is_null($user->first_login_at)) {
                $user->first_login_at = Carbon::now();
                $user->save();
            }

            if (empty($user->api_token)) {
                $token = Str::random(60);
                $user->api_token = hash('sha256', $token);
                $user->save();
            }

            return response()->json([
                'token' => $user->api_token,
            ], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // public function register(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //         'selectedPackage' => ['required', 'integer'],
    //         'selectedPackageName' => ['required', 'string']
    //     ]);

    //     $activationToken = Str::random(60);

    //     $package = Package::where('id', $validatedData['selectedPackage'])->firstOrFail();

    //     $user = User::create([
    //         'name' => $validatedData['name'],
    //         'email' => $validatedData['email'],
    //         'password' => Hash::make($validatedData['password']),
    //         'selected_package' => $validatedData['selectedPackageName'],
    //         'package_id' => $validatedData['selectedPackage'],
    //         'activation_token' => $activationToken,
    //         'role_id' => 3,
    //     ]);

    //     Payment::create([
    //         'user_id' => $user->id,
    //         'package_id' => $package->id,
    //         'amount' => $package->price,
    //         'status' => 'pending',
    //         // 'paid_at' => now(),
    //     ]);

    //     // $user = (object) [
    //     //     'name' => 'Roqeeb Yusuff',
    //     // ];

    //     // $name = $user->name;

    //     // $activationToken = Str::random(60);

    //     // Mail::to($user->email)->send(new AccountActivationMail($user, $activationToken));

    //     // Mail::to('roqeebyusuff17@gmail.com')->send(new AccountActivationMail($user, $activationToken));



    //     $token = Str::random(60);
    //     $user->api_token = hash('sha256', $token);
    //     $user->save();

    //     return response()->json(['user' => $user, 'token' => $user->api_token, 'activationToken' => $activationToken], 201);
    // }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'selectedPackage' => ['required', 'integer'],
            'selectedPackageName' => ['required', 'string']
        ]);

        $activationToken = Str::random(60);

        $package = Package::where('id', $validatedData['selectedPackage'])->firstOrFail();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'selected_package' => $validatedData['selectedPackageName'],
            'package_id' => $validatedData['selectedPackage'],
            'activation_token' => $activationToken,
            'role_id' => 3,
        ]);

        Payment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'status' => 'pending',
        ]);

        // Send the activation email to the user
        Mail::to($user->email)->send(new AccountActivationMail($user, $activationToken));

        // Optional: Send a notification email to an admin or a specific address
        // Mail::to('admin@example.com')->send(new AccountActivationMail($user, $activationToken));

        $token = Str::random(60);
        $user->api_token = hash('sha256', $token);
        $user->save();

        return response()->json(['user' => $user, 'token' => $user->api_token, 'activationToken' => $activationToken], 201);
    }



    public function logout(Request $request)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $user->api_token = null;
        $user->save();

        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function activateAccount($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid activation token.'], 404);
        }

        $user->is_active = true;
        $user->activation_token = null;
        $user->save();

        return response()->json(['message' => 'Account activated successfully.']);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $validatedData = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8'],
        ]);

        if (isset($validatedData['name'])) {
            $user->name = $validatedData['name'];
        }

        if (isset($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return response()->json(['user' => $user]);
    }



    // public function sendTestEmail()
    // {
    //     $toEmail = 'gdsa006@gmail.com';

    //     Mail::raw('This is a test email.', function ($message) use ($toEmail) {
    //         $message->to($toEmail)
    //                 ->subject('Test Email');
    //     });

    //     return 'Test email sent successfully!';
    // }

    public function getUserData(Request $request)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $user->load('payments');

        return response()->json($user);
    }

    public function getUserRole(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $userWithRole = User::with('role')->find($user->id);

        if (!$userWithRole) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['role' => $userWithRole->role->name]);
    }

    public function updatePackage(Request $request)
    {
        $validatedData = $request->validate([
            'selectedPackage' => 'required|integer',
            'selectedPackageName' => 'required|string'
        ]);

        $user = Auth::user();

        if ($user instanceof User) {
            $package = Package::where('id', $validatedData['selectedPackage'])->firstOrFail();

            $user->selected_package = $package->name;
            $user->package_id = $package->id;
            $user->save();

            $payment = new Payment();
            $payment->user_id = $user->id;
            $payment->package_id = $package->id;
            $payment->amount = $package->price;
            $payment->status = 'pending';
            $payment->paid_at = now();
            $payment->save();

            return response()->json(['message' => 'Package updated and payment logged successfully'], 200);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }


    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email address not found.'], 404);
        }

        $token = Str::random(60);
        $hashedToken = hash('sha256', $token);

        // Log the token before and after hashing
        Log::info("Token before hashing: " . $token);
        Log::info("Hashed token: " . $hashedToken);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $hashedToken, 'created_at' => now()]
        );

        $storedToken = DB::table('password_resets')->where('email', $user->email)->value('token');
        Log::info("Stored token: " . $storedToken);

        Mail::to($user->email)->send(new PasswordResetMail($token, $user));

        return response()->json(['message' => 'Password reset link sent to your email.'], 200);
    }



    public function validateResetToken(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $hashedToken = hash('sha256', $request->token);
        $resetRecord = DB::table('password_resets')
            ->where('token', $hashedToken)
            ->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            return response()->json(['error' => 'Invalid or expired token.'], 400);
        }

        return response()->json(['message' => 'Token is valid.'], 200);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        $token = $request->input('token');
        $hashedToken = hash('sha256', $token);

        Log::info("Received token: " . $token);
        Log::info("Hashed token: " . $hashedToken);

        $record = DB::table('password_resets')->where('token', $hashedToken)->first();
        if (!$record) {
            return response()->json(['message' => 'Invalid or expired token.'], 400);
        }

        if (Carbon::parse($record->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            return response()->json(['message' => 'Token has expired.'], 400);
        }

        $user = User::where('email', $record->email)->first();
        Log::info("User retrieved from User model: " . json_encode($user));

        if (!$user) {
            return response()->json(['message' => 'We can\'t find a user with that email address.'], 404);
        }

        try {
            $user->password = Hash::make($request->password);
            $user->save();

            DB::table('password_resets')->where('token', $hashedToken)->delete();

            return response()->json(['message' => 'Password has been reset successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Password reset failed: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while resetting the password.'], 500);
        }
    }
}
