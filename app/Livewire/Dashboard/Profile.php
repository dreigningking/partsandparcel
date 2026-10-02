<?php

namespace App\Livewire\Dashboard;

use App\Models\BankAccount;
use App\Models\Country;
use App\Models\DeviceToken;
use App\Models\Location;
use App\Services\Notification\FcmService;
use App\Services\Payment\PaystackService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dash')]
class Profile extends Component
{
    use WithFileUploads;

    // Active Navigation Subtab
    public string $activeSection = 'profile'; // profile, security, notifications, banking

    // User Profile Fields
    public string $name = '';
    public string $business_name = '';
    public string $email = '';
    public string $phone = '';
    public ?int $country_id = null;
    public string $bio = '';
    public string $theme_preference = 'system';
    public ?string $currentAvatar = null;
    public $avatarFile = null;

    // Password Change Fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Notification Preferences
    public bool $notify_in_app = true;
    public bool $notify_push = true;
    public bool $notify_email = true;

    // Bank Account Details
    public string $bank_name = '';
    public string $bank_code = '';
    public string $account_number = '';
    public string $account_name = '';
    public string $bank_password = '';

    // Gateway Bank Selection & Resolution State
    public array $banks = [];
    public bool $isLoadingBanks = false;
    public bool $isResolving = false;
    public bool $resolveSuccess = false;
    public ?string $resolveError = null;

    // Existing Saved Bank
    public ?BankAccount $savedBank = null;
    public bool $hasSavedBank = false;
    public bool $showEditBankForm = false;

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->name = (string) ($user->name ?? '');
            $this->business_name = (string) ($user->business_name ?? '');
            $this->email = (string) ($user->email ?? '');
            $this->phone = (string) ($user->phone ?? '');
            $this->country_id = $user->country_id ? (int) $user->country_id : null;
            $this->bio = (string) ($user->bio ?? '');
            $this->theme_preference = in_array($user->theme_preference, ['light', 'dark', 'system']) 
                ? $user->theme_preference 
                : 'system';
            $this->currentAvatar = $user->avatar;

            // Notification preferences
            $this->notify_in_app = $user->notificationPreference('in_app');
            $this->notify_push = $user->notificationPreference('push');
            $this->notify_email = $user->notificationPreference('email');

            // Bank details
            $this->savedBank = $user->bankAccounts()->where('is_default', true)->first() 
                ?: $user->bankAccounts()->first();

            if ($this->savedBank) {
                $this->hasSavedBank = true;
                $this->bank_name = $this->savedBank->bank_name ?? '';
                $this->bank_code = $this->savedBank->bank_code ?? '';
                $this->account_number = $this->savedBank->account_number ?? '';
                $this->account_name = $this->savedBank->account_name ?? '';
            } else {
                $this->showEditBankForm = true;
            }
        }
    }

    public function setSection(string $section)
    {
        if (in_array($section, ['profile', 'security', 'notifications', 'banking'])) {
            $this->activeSection = $section;
        }
    }

    public function saveProfile()
    {
        $user = Auth::user();

        $this->validate([
            'name' => 'required|string|max:100',
            'business_name' => 'nullable|string|max:150',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:25',
            'country_id' => 'nullable|exists:countries,id',
            'bio' => 'nullable|string|max:1000',
            'theme_preference' => 'required|in:system,light,dark',
            'avatarFile' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $this->name,
            'business_name' => $this->business_name ?: null,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'country_id' => $this->country_id ?: $user->country_id,
            'bio' => $this->bio ?: null,
            'theme_preference' => $this->theme_preference,
        ];

        if ($this->avatarFile) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $this->avatarFile->store('avatars', 'public');
            $this->currentAvatar = $data['avatar'];
            $this->avatarFile = null;
        }

        $user->update($data);

        session()->flash('profile_success', 'Profile information updated successfully.');
    }

    public function removeAvatar()
    {
        $user = Auth::user();
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->update(['avatar' => null]);
        $this->currentAvatar = null;
        $this->avatarFile = null;

        session()->flash('profile_success', 'Profile photo removed.');
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'new_password.different' => 'The new password must be different from your current password.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
        ]);

        $user = Auth::user();

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password provided is incorrect.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('password_success', 'Your password has been changed securely.');
    }

    public function saveNotificationPreferences()
    {
        $user = Auth::user();
        $user->update([
            'notification_preferences' => [
                'in_app' => (bool) $this->notify_in_app,
                'push' => (bool) $this->notify_push,
                'email' => (bool) $this->notify_email,
            ],
        ]);

        session()->flash('notification_success', 'Notification preferences updated successfully.');
    }

    public function removeDeviceToken(int $tokenId)
    {
        $user = Auth::user();
        $user->deviceTokens()->where('id', $tokenId)->delete();
        session()->flash('device_success', 'Device removed from push notifications.');
    }

    public function registerCurrentDevice(?string $token = null, string $platform = 'web', ?string $deviceName = null)
    {
        $user = Auth::user();
        $rawAgent = request()->header('User-Agent', 'Web Browser');
        
        $browserName = 'Web Browser';
        if (str_contains($rawAgent, 'Chrome')) {
            $browserName = 'Google Chrome';
        } elseif (str_contains($rawAgent, 'Firefox')) {
            $browserName = 'Mozilla Firefox';
        } elseif (str_contains($rawAgent, 'Safari')) {
            $browserName = 'Apple Safari';
        } elseif (str_contains($rawAgent, 'Edge')) {
            $browserName = 'Microsoft Edge';
        }

        $token = $token ?: 'fcm_token_' . md5($user->id . '_' . $rawAgent . '_' . now()->timestamp);
        $deviceName = $deviceName ?: ($browserName . ' (' . (PHP_OS_FAMILY ?? 'PC') . ')');

        app(FcmService::class)->registerToken($user, $token, $platform, $deviceName);
        session()->flash('device_success', "Device '{$deviceName}' registered for push notifications.");
    }

    public function testPushNotification(FcmService $fcm)
    {
        $user = Auth::user();
        if ($user->deviceTokens()->count() === 0) {
            session()->flash('device_error', 'No registered devices found. Click "Register This Browser" below to test push notifications.');
            return;
        }

        $result = $fcm->sendToUser(
            $user, 
            'Parts & Parcel Alert', 
            'Push notifications are working properly on your device!',
            [
                'type' => 'test_push',
                'url' => route('profile'),
                'timestamp' => now()->toISOString(),
            ]
        );

        $sentCount = $result['sent_count'] ?? 0;
        session()->flash('device_success', "Test push notification dispatched to {$sentCount} active device(s).");
    }

    /**
     * Triggered immediately after page finishes loading via wire:init
     */
    public function loadBanks(PaystackService $paystack)
    {
        $this->isLoadingBanks = true;
        $fetchedBanks = $paystack->getBanks('NG');

        if (! empty($fetchedBanks)) {
            $this->banks = $fetchedBanks;

            // If bank_name is already present and matches a bank, link bank_code
            if ($this->bank_name && empty($this->bank_code)) {
                $match = collect($this->banks)->first(function ($b) {
                    return strtolower(trim($b['name'])) === strtolower(trim($this->bank_name));
                });
                if ($match) {
                    $this->bank_code = (string) $match['code'];
                }
            }
        }

        $this->isLoadingBanks = false;
    }

    public function updatedBankCode($value)
    {
        $selected = collect($this->banks)->firstWhere('code', $value);
        if ($selected) {
            $this->bank_name = $selected['name'];
        }

        if (strlen(trim($this->account_number)) === 10) {
            $this->resolveAccountName(app(PaystackService::class));
        }
    }

    public function updatedAccountNumber($value)
    {
        $clean = preg_replace('/\D/', '', (string) $value);
        $this->account_number = substr($clean, 0, 10);

        if (strlen($this->account_number) === 10 && ! empty($this->bank_code)) {
            $this->resolveAccountName(app(PaystackService::class));
        } else {
            $this->resolveSuccess = false;
            $this->resolveError = null;
        }
    }

    public function resolveAccountName(PaystackService $paystack)
    {
        if (strlen($this->account_number) !== 10 || empty($this->bank_code)) {
            return;
        }

        $this->isResolving = true;
        $this->resolveError = null;

        $resolved = $paystack->resolveAccount($this->bank_code, $this->account_number);

        if ($resolved) {
            $this->account_name = $resolved;
            $this->resolveSuccess = true;
            $this->resolveError = null;
        } else {
            $this->resolveSuccess = false;
            $this->resolveError = 'Could not automatically verify account name. You may type it in manually below.';
        }

        $this->isResolving = false;
    }

    public function saveBankAccount()
    {
        $this->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|digits:10',
            'account_name' => 'required|string|max:150',
            'bank_password' => 'required|string',
        ], [
            'bank_password.required' => 'Please enter your account password to authorize saving bank details.',
            'account_number.digits' => 'Account number must be exactly 10 digits.',
        ]);

        $user = Auth::user();

        if (! Hash::check($this->bank_password, $user->password)) {
            $this->addError('bank_password', 'Incorrect account password. Please enter your valid password to confirm changes.');
            return;
        }

        $this->savedBank = BankAccount::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'bank_name' => $this->bank_name,
                'bank_code' => $this->bank_code ?: null,
                'account_number' => $this->account_number,
                'account_name' => $this->account_name,
                'currency' => $user->country?->currency ?? 'NGN',
                'is_default' => true,
                'verified_at' => $this->resolveSuccess ? now() : ($this->savedBank?->verified_at ?? null),
            ]
        );

        $this->hasSavedBank = true;
        $this->showEditBankForm = false;
        $this->bank_password = '';

        session()->flash('bank_success', 'Your bank account has been securely saved and will receive direct transfers and platform payouts.');
    }

    public function editBank()
    {
        $this->showEditBankForm = true;
    }

    public function cancelEditBank()
    {
        if ($this->hasSavedBank && $this->savedBank) {
            $this->bank_name = $this->savedBank->bank_name ?? '';
            $this->bank_code = $this->savedBank->bank_code ?? '';
            $this->account_number = $this->savedBank->account_number ?? '';
            $this->account_name = $this->savedBank->account_name ?? '';
            $this->bank_password = '';
            $this->showEditBankForm = false;
        }
    }

    public function render()
    {
        $user = Auth::user();
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $deviceTokens = $user ? $user->deviceTokens()->latest()->get() : collect();

        return view('livewire.dashboard.profile', [
            'user' => $user,
            'countries' => $countries,
            'deviceTokens' => $deviceTokens,
        ]);
    }
}
