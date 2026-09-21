<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Register - Parts & Parcel')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $business_name = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'business_name' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ];
    }

    public function submit()
    {
        $this->validate();

        $locationContext = session('current_location', []);
        $countryCode = $locationContext['country_code'] ?? 'NG';
        $currency = $locationContext['currency'] ?? 'NGN';

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'business_name' => $this->business_name ?: null,
            'password' => Hash::make($this->password),
            'role_id' => null, // Standard user (can buy, sell, request, and offer services)
            'country_code' => $countryCode,
            'currency' => $currency,
            'theme_preference' => 'system',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
