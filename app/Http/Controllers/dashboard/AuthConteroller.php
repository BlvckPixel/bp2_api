<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\AccountActivationMail;

class AuthConteroller extends Controller
{
    public function createModerator(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'numeric', 'in:2,3'],
        ]);

        $activationToken = Str::random(60);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => $validatedData['role_id'],
            'activation_token' => $activationToken,
        ]);

        Mail::to($user->email)->send(new AccountActivationMail($user, $activationToken));

        return response()->json(['message' => 'Moderator created successfully', 'user' => $user], 201);
    }
}
