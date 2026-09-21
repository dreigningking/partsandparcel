<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Country::updateOrCreate(['code' => 'NG'], [
            'name' => 'Nigeria',
            'phone_code' => '+234',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'timezone' => 'Africa/Lagos',
            'is_active' => true,
        ]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $email = 'register_' . uniqid() . '@example.com';

        Livewire::test(Register::class)
            ->set('name', 'Samuel Eze')
            ->set('email', $email)
            ->set('phone', '+2348099990000')
            ->set('business_name', 'Eze Motors & Parts')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('terms', true)
            ->call('submit')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'Samuel Eze',
            'business_name' => 'Eze Motors & Parts',
            'country_code' => 'NG',
            'currency' => 'NGN',
            'role_id' => null,
        ]);
    }

    public function test_user_can_authenticate_using_login(): void
    {
        $email = 'login_' . uniqid() . '@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('secret123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $email)
            ->set('password', 'secret123')
            ->call('submit')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $email = 'invalid_' . uniqid() . '@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('secret123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $email)
            ->set('password', 'wrong-password')
            ->call('submit');

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post('/logout');

        $response->assertRedirect(route('welcome'));
        $this->assertFalse(Auth::check());
    }
}
