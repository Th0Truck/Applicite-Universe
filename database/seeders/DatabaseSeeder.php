<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Thomas Troelsen',
            'email' => 'thotruck@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('super_admin');
    }
}
