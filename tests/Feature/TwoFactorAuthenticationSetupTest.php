<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Fortify;
use Tests\TestCase;

class TwoFactorAuthenticationSetupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guests are sent to the login page before viewing two-factor setup.
     */
    public function test_guest_is_redirected_from_two_factor_setup_page(): void
    {
        $this->get(route('two-factor.show'))
            ->assertRedirect(route('login'));
    }

    /**
     * Authenticated users can view the two-factor setup page.
     */
    public function test_user_can_view_two_factor_setup_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('two-factor.show'))
            ->assertOk()
            ->assertSee('Enable two-factor authentication');
    }

    /**
     * Enabled two-factor authentication exposes the QR setup state.
     */
    public function test_enabled_two_factor_authentication_can_render_setup_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('two-factor.enable'))
            ->assertRedirect();

        $user->refresh();

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);

        $this->actingAs($user)
            ->get(route('two-factor.show'))
            ->assertOk()
            ->assertSee('Scan this QR code')
            ->assertSee(Fortify::currentEncrypter()->decrypt($user->two_factor_secret));
    }
}
