<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopbarNavigationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guests see authentication actions in the topbar.
     */
    public function test_guest_sees_login_and_signup_links(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Login')
            ->assertSee('Sign up')
            ->assertDontSee('Dashboard');
    }

    /**
     * Authenticated users see dashboard and logout actions in the topbar.
     */
    public function test_authenticated_user_sees_dashboard_and_logout_links(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Logout')
            ->assertDontSee('Sign up');
    }

    /**
     * Dashboard is only available to authenticated users.
     */
    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Welcome back, '.$user->name);
    }
}
