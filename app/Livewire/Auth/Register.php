<?php

namespace App\Livewire\Auth;

use App\Models\Country;
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
    public ?int $country_id = null;

    public function mount(): void
    {
        $this->country_id = Country::where('is_default', true)->value('id') ?? Country::first()?->id;
    }

    protected function rules(): array
    {
        return [
            'country_id' => 'required|integer|exists:countries,id',
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

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'business_name' => $this->business_name ?: null,
            'password' => Hash::make($this->password),
            'role_id' => null, // Standard user (can buy, sell, request, and offer services)
            'country_id' => $this->country_id,
            'theme_preference' => 'system',
        ]);

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function render()
    {
        return view('livewire.auth.register',[
            'countries' => Country::query()
                ->where('is_active', true)
                ->orderBy('name','asc')
                ->get(['id', 'name', 'flag']),
        ]);
    }
}
