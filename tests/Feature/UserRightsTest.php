<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UserRightsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The access seeder creates the default roles and permissions.
     */
    public function test_default_roles_and_permissions_are_seeded(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create();
        $manager = User::factory()->create();

        $admin->assignRole('admin');
        $manager->assignRole('manager');

        $this->assertTrue($admin->can('settings.manage'));
        $this->assertTrue($admin->can('passkeys.manage'));
        $this->assertTrue($manager->can('users.view'));
        $this->assertFalse($manager->can('settings.manage'));
    }

    /**
     * Super admins can pass future Gate checks without explicit permissions.
     */
    public function test_super_admin_role_passes_gate_checks(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->assertTrue(Gate::forUser($user)->allows('future.permission'));
    }

    /**
     * Permission middleware protects routes using the seeded permissions.
     */
    public function test_permission_middleware_uses_seeded_permissions(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        Route::middleware(['web', 'auth', 'permission:settings.manage'])
            ->get('/test/settings-rights', fn (): string => 'allowed');

        $admin = User::factory()->create();
        $manager = User::factory()->create();

        $admin->assignRole('admin');
        $manager->assignRole('manager');

        $this->actingAs($admin)
            ->get('/test/settings-rights')
            ->assertOk()
            ->assertSee('allowed');

        $this->actingAs($manager)
            ->get('/test/settings-rights')
            ->assertForbidden();
    }
}
