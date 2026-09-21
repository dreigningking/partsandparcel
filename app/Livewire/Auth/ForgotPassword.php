<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Forgot Password - Parts & Parcel')]
class ForgotPassword extends Component
{
    public string $email = '';
    public string $statusMessage = '';
    public string $errorMessage = '';

    protected function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function submit()
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->statusMessage = __($status);
            $this->errorMessage = '';
        } else {
            $this->errorMessage = __($status);
            $this->statusMessage = '';
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
