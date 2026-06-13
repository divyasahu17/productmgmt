<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Mail\PasswordResetOtpMail;

class PasswordResetController extends Controller
{
    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        // Generate OTP
        $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Delete existing reset tokens for this email
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Save new token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $otpCode,
            'created_at' => now(),
        ]);

        // Send Email
        Mail::to($request->email)->send(new PasswordResetOtpMail($otpCode));

        return response()->json(['message' => 'OTP sent to your email.']);
    }

    public function verifyOtpAndReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP.']
            ]);
        }

        // Check expiration (15 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 15) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            throw ValidationException::withMessages([
                'otp' => ['This OTP has expired.']
            ]);
        }

        // Valid OTP, reset password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete record
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Send confirmation email
        Mail::to($user->email)->send(new \App\Mail\PasswordChangedMail());

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
