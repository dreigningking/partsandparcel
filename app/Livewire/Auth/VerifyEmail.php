<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Notifications\EmailVerificationOtpNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Tzsk\Otp\Facades\Otp;

#[Layout('layouts.app')]
#[Title('Verify Your Email - Parts & Parcel')]
class VerifyEmail extends Component
{
    public string $email = '';
    public string $otp = '';
    public bool $isEditingEmail = false;
    public string $newEmail = '';
    public string $statusMessage = '';
    public string $errorMessage = '';
    public int $resendCooldown = 0;

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user) {
            redirect()->route('login');
            return;
        }

        if ($user->hasVerifiedEmail()) {
            redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'dashboard');
            return;
        }

        $this->email = (string) $user->email;
        $this->newEmail = (string) $user->email;

        // Auto-send OTP on first landing if not sent recently
        $lastSent = Cache::get($this->cooldownCacheKey());
        if (! $lastSent) {
            $this->sendOtpNotification($user);
            $this->statusMessage = 'A 6-digit verification code has been sent to your email.';
        } else {
            $remaining = (int) ($lastSent - time());
            $this->resendCooldown = max(0, $remaining);
        }
    }

    protected function otpKey(): string
    {
        return 'email_verify_' . Auth::id();
    }

    protected function cooldownCacheKey(): string
    {
        return 'email_verify_cooldown_' . Auth::id();
    }

    protected function sendOtpNotification(User $user): void
    {
        // Generate 6-digit OTP valid for 10 minutes
        $code = Otp::digits(6)->expiry(10)->generate($this->otpKey());

        // Send Email Notification
        $user->notify(new EmailVerificationOtpNotification($code, 10));

        // Set 60-second cooldown in cache
        $expiry = time() + 60;
        Cache::put($this->cooldownCacheKey(), $expiry, 60);
        $this->resendCooldown = 60;
    }

    public function resendOtp(): void
    {
        $this->resetErrorBag();
        $this->errorMessage = '';

        $user = Auth::user();
        if (! $user) {
            redirect()->route('login');
            return;
        }

        $lastSent = Cache::get($this->cooldownCacheKey());
        if ($lastSent && $lastSent > time()) {
            $this->resendCooldown = (int) ($lastSent - time());
            $this->errorMessage = "Please wait {$this->resendCooldown} seconds before requesting another code.";
            return;
        }

        $this->sendOtpNotification($user);
        $this->statusMessage = 'A fresh 6-digit verification code has been sent to ' . $this->email . '.';
    }

    public function toggleEditEmail(): void
    {
        $this->isEditingEmail = ! $this->isEditingEmail;
        $this->newEmail = $this->email;
        $this->resetErrorBag();
        $this->errorMessage = '';
    }

    public function updateEmail(): void
    {
        $user = Auth::user();
        if (! $user) {
            redirect()->route('login');
            return;
        }

        $this->validate([
            'newEmail' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        if (strtolower(trim($this->newEmail)) === strtolower(trim($user->email))) {
            $this->isEditingEmail = false;
            return;
        }

        $user->email = trim($this->newEmail);
        $user->email_verified_at = null;
        $user->save();

        $this->email = $user->email;
        $this->isEditingEmail = false;

        // Forget old OTP and send fresh OTP to updated email
        Otp::forget($this->otpKey());
        Cache::forget($this->cooldownCacheKey());

        $this->sendOtpNotification($user);
        $this->statusMessage = 'Your email was updated to ' . $this->email . '. A new verification code has been sent.';
    }

    public function verify(): void
    {
        $this->resetErrorBag();
        $this->errorMessage = '';
        $this->statusMessage = '';

        $this->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Please enter the 6-digit verification code.',
            'otp.size' => 'The verification code must be exactly 6 digits.',
        ]);

        $user = Auth::user();
        if (! $user) {
            redirect()->route('login');
            return;
        }

        $cleanOtp = trim($this->otp);
        $isValid = Otp::digits(6)->check($cleanOtp, $this->otpKey());

        if (! $isValid) {
            $this->addError('otp', 'The verification code entered is invalid or has expired. Please check and try again or request a new code.');
            return;
        }

        // Clean up OTP key
        Otp::forget($this->otpKey());
        Cache::forget($this->cooldownCacheKey());

        // Mark verified
        $user->markEmailAsVerified();

        session()->flash('status', 'Your email address has been verified successfully!');

        if ($user->isAdmin()) {
            redirect()->route('admin.dashboard');
            return;
        }

        redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}
