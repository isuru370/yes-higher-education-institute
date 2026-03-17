<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot_password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No user found with this email address.'
            ])->withInput();
        }

        // old unused OTPs remove
        PasswordResetOtp::where('email', $request->email)
            ->whereNull('verified_at')
            ->delete();

        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(3),
        ]);

        Mail::to($request->email)->send(new PasswordResetMail($otp, $user->name ?? null));

        return back()->with([
            'success' => 'OTP sent to your email.',
            'email' => $request->email,
            'step' => 'otp'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $record = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$record) {
            return back()->withErrors([
                'otp' => 'Invalid OTP.'
            ])->with([
                'email' => $request->email,
                'step' => 'otp'
            ]);
        }

        if (Carbon::now()->gt($record->expires_at)) {
            return back()->withErrors([
                'otp' => 'OTP expired. Please resend OTP.'
            ])->with([
                'email' => $request->email,
                'step' => 'otp',
                'otp_expired' => true
            ]);
        }

        $record->update([
            'verified_at' => Carbon::now()
        ]);

        return back()->with([
            'success' => 'OTP verified successfully.',
            'email' => $request->email,
            'step' => 'reset'
        ]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No user found with this email address.'
            ]);
        }

        PasswordResetOtp::where('email', $request->email)
            ->whereNull('verified_at')
            ->delete();

        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(3),
        ]);

        Mail::to($request->email)->send(new PasswordResetMail($otp, $user->name ?? null));

        return back()->with([
            'success' => 'New OTP sent successfully.',
            'email' => $request->email,
            'step' => 'otp'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $verifiedOtp = PasswordResetOtp::where('email', $request->email)
            ->whereNotNull('verified_at')
            ->latest()
            ->first();

        if (!$verifiedOtp) {
            return back()->withErrors([
                'email' => 'OTP verification required first.'
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'User not found.'
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        PasswordResetOtp::where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully. Please login.');
    }
}
