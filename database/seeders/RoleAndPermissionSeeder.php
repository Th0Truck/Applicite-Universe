<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Seed the application's roles and permissions.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = $this->permissionNames();

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        foreach (config('access.roles', []) as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions($this->permissionsForRole($rolePermissions, $permissions));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Get a flattened, unique list of configured permission names.
     *
     * @return array<int, string>
     */
    private function permissionNames(): array
    {
        return collect(config('access.permissions', []))
            ->flatMap(fn (array $permissions): array => $permissions)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Resolve wildcard role permissions to concrete permission names.
     *
     * @param  array<int, string>  $rolePermissions
     * @param  array<int, string>  $permissions
     * @return array<int, string>
     */
    private function permissionsForRole(array $rolePermissions, array $permissions): array
    {
        if (in_array('*', $rolePermissions, true)) {
            return $permissions;
        }

        return Arr::where($rolePermissions, fn (string $permission): bool => in_array($permission, $permissions, true));
    }
}
