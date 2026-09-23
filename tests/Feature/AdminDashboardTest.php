<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role_id' => null]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_admin_user_can_access_admin_dashboard_and_see_metrics(): void
    {
        $role = Role::create([
            'name' => 'Super Administrator',
            'slug' => 'super_admin',
            'description' => 'Platform admin',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'name' => 'Root Admin',
            'role_id' => $role->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Platform control center');
        $response->assertSee('GMV this month');
        $response->assertSee('MARKETPLACE');
    }

    public function test_admin_user_sees_admin_nav_in_sidebar(): void
    {
        $role = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Admin role',
            'is_active' => true,
        ]);

        $admin = User::factory()->create(['role_id' => $role->id]);
        $regularUser = User::factory()->create(['role_id' => null]);

        // Regular user sidebar
        $resUser = $this->actingAs($regularUser)->get('/dashboard');
        $resUser->assertSee('General Navigation');

        // Admin user sidebar
        $resAdmin = $this->actingAs($admin)->get('/admin');
        $resAdmin->assertSee('MARKETPLACE');
        $resAdmin->assertSee('TRUST &amp; RESOLUTION', false);
    }

    public function test_admin_user_can_switch_between_admin_and_user_console(): void
    {
        $role = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'is_active' => true,
        ]);

        $admin = User::factory()->create(['role_id' => $role->id]);

        // When in admin console:
        $adminView = $this->actingAs($admin)->get('/admin/dashboard');
        $adminView->assertStatus(200);
        $adminView->assertSee('MARKETPLACE');
        $adminView->assertSee('User Dashboard');

        // When admin switches to user console:
        $userView = $this->actingAs($admin)->get('/dashboard');
        $userView->assertStatus(200);
        $userView->assertSee('General Navigation');
        $userView->assertSee('Admin Console');
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $role = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'email' => 'admin_switch@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role_id' => $role->id,
        ]);

        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', 'admin_switch@example.com')
            ->set('password', 'password123')
            ->call('submit')
            ->assertRedirect(route('admin.dashboard'));
    }
}
