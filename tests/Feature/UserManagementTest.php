<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Users without the user view permission cannot open user management.
     */
    public function test_users_index_requires_user_view_permission(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $regularUser = User::factory()->create();

        $this->actingAs($regularUser)
            ->get(route('dashboard.users.index'))
            ->assertForbidden();
    }

    /**
     * Users with user view permission can list users.
     */
    public function test_users_with_permission_can_list_users(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $manager = User::factory()->create();
        $targetUser = User::factory()->create();

        $manager->assignRole('manager');

        $this->actingAs($manager)
            ->get(route('dashboard.users.index'))
            ->assertOk()
            ->assertSee($targetUser->email);
    }

    /**
     * Users with update permission can edit account details.
     */
    public function test_user_details_can_be_updated_by_user_manager(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $manager = User::factory()->create();
        $targetUser = User::factory()->create();

        $manager->assignRole('manager');

        $this->actingAs($manager)
            ->put(route('dashboard.users.update', $targetUser), [
                'name' => 'Updated User',
                'email' => 'updated@example.com',
            ])
            ->assertRedirect(route('dashboard.users.edit', $targetUser));

        $targetUser->refresh();

        $this->assertSame('Updated User', $targetUser->name);
        $this->assertSame('updated@example.com', $targetUser->email);
    }

    /**
     * Role assignment requires role management rights.
     */
    public function test_role_assignment_requires_role_management_permission(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $manager = User::factory()->create();
        $targetUser = User::factory()->create();

        $manager->assignRole('manager');

        $this->actingAs($manager)
            ->put(route('dashboard.users.roles.update', $targetUser), [
                'roles' => ['admin'],
            ])
            ->assertForbidden();

        $this->assertFalse($targetUser->fresh()->hasRole('admin'));
    }

    /**
     * Super admins can assign proper rights to users.
     */
    public function test_super_admin_can_assign_roles_to_users(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $superAdmin = User::factory()->create();
        $targetUser = User::factory()->create();

        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)
            ->put(route('dashboard.users.roles.update', $targetUser), [
                'roles' => ['admin', 'manager'],
            ])
            ->assertRedirect(route('dashboard.users.edit', $targetUser));

        $targetUser->refresh();

        $this->assertTrue($targetUser->hasRole('admin'));
        $this->assertTrue($targetUser->hasRole('manager'));
    }
}
