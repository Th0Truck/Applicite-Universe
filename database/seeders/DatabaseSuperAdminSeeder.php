<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSuperAdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        // User::factory(10)->create();

        // TODO; Remove this and create a proper user seeder that creates a super admin user.
        // Add this to the documentation and make sure to change the password before deploying to production.
        // Consider adding this to gitignore and add a note about it in the documentation to avoid accidentally committing it to version control.

        $user = User::factory()->create([
            'name' => 'Thomas Troelsen',
            'email' => 'thotruck@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('super_admin');
    }
}
