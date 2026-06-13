<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use App\Models\EmailUpdateOtp;
use App\Mail\EmailUpdateOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        if ($request->email !== $user->email) {
            // Generate OTP
            $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            // Delete any existing OTP for this user
            EmailUpdateOtp::where('user_id', $user->id)->delete();

            // Create new OTP
            EmailUpdateOtp::create([
                'user_id' => $user->id,
                'new_email' => $request->email,
                'otp' => $otpCode,
                'expires_at' => now()->addMinutes(15),
            ]);

            // Send OTP to CURRENT email
            Mail::to($user->email)->send(new EmailUpdateOtpMail($otpCode));

            // Only update name for now
            $user->update(['name' => $request->name]);

            return response()->json([
                'message' => 'OTP sent to your current email address.',
                'requires_otp' => true,
                'user' => new UserResource($user),
            ]);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }

    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();
        
        $otpRecord = EmailUpdateOtp::where('user_id', $user->id)
                                   ->where('otp', $request->otp)
                                   ->first();

        if (!$otpRecord) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP.'],
            ]);
        }

        if ($otpRecord->expires_at->isPast()) {
            $otpRecord->delete();
            throw ValidationException::withMessages([
                'otp' => ['This OTP has expired. Please request a new one.'],
            ]);
        }

        // OTP is valid and not expired, update email
        $user->update(['email' => $otpRecord->new_email]);

        // Delete OTP record
        $otpRecord->delete();

        return response()->json([
            'message' => 'Email updated successfully',
            'user' => new UserResource($user),
        ]);
    }
}
