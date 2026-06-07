<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Admins are redirected to the dashboard after login.
     */
    public function test_admin_is_redirected_to_dashboard_after_login(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = $this->userWithRole('admin');

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    /**
     * Super admins are redirected to the dashboard after login.
     */
    public function test_super_admin_is_redirected_to_dashboard_after_login(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = $this->userWithRole('super_admin');

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    /**
     * Regular users are redirected to the site root after login.
     */
    public function test_regular_user_is_redirected_to_site_root_after_login(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = $this->userWithRole('user');

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(url('/'));
    }

    /**
     * Create a user with a known password and role.
     */
    private function userWithRole(string $role): User
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $user->assignRole($role);

        return $user;
    }
}
