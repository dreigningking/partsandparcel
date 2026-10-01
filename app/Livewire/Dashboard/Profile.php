<?php

namespace App\Livewire\Dashboard;

use App\Models\BankAccount;
use App\Models\Location;
use App\Services\Payment\PaystackService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class Profile extends Component
{
    // User Profile
    public string $name = '';
    public string $business_name = '';
    public string $email = '';
    public string $phone = '';

    // Bank Account Details
    public string $bank_name = '';
    public string $bank_code = '';
    public string $account_number = '';
    public string $account_name = '';
    public string $password = '';

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

    public function saveProfile()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'business_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:25',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $this->name,
            'business_name' => $this->business_name,
            'phone' => $this->phone,
        ]);

        session()->flash('profile_success', 'Profile information updated successfully.');
    }

    public function saveBankAccount()
    {
        $this->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|digits:10',
            'account_name' => 'required|string|max:150',
            'password' => 'required|string',
        ], [
            'password.required' => 'Please enter your account password to authorize saving bank details.',
            'account_number.digits' => 'Account number must be exactly 10 digits.',
        ]);

        $user = Auth::user();

        // 3. User must enter account password before saving
        if (! Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Incorrect account password. Please enter your valid password to confirm changes.');
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
                'currency' => $user->currency ?? 'NGN',
                'is_default' => true,
                'verified_at' => $this->resolveSuccess ? now() : ($this->savedBank?->verified_at ?? null),
            ]
        );

        $this->hasSavedBank = true;
        $this->showEditBankForm = false;
        $this->password = '';

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
            $this->password = '';
            $this->showEditBankForm = false;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.profile');
    }
}
