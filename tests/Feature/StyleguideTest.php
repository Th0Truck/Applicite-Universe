<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StyleguideTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guests cannot view the internal styleguide.
     */
    public function test_guest_is_redirected_from_styleguide(): void
    {
        $this->get(route('styleguide'))
            ->assertRedirect(route('login'));
    }

    /**
     * Authenticated users can view the internal styleguide sections.
     */
    public function test_authenticated_user_can_view_styleguide(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('styleguide'))
            ->assertOk()
            ->assertSee('Universe Styleguide')
            ->assertSee('Brand Principles')
            ->assertSee('Color Palette')
            ->assertSee('Typography Scale')
            ->assertSee('Spacing Scale')
            ->assertSee('Buttons')
            ->assertSee('Form Elements')
            ->assertSee('Cards')
            ->assertSee('Badges')
            ->assertSee('Alerts')
            ->assertSee('Tables')
            ->assertSee('Layout Examples')
            ->assertSee('Accessibility Rules');
    }
}
