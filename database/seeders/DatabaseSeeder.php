<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $teacherRole = \Spatie\Permission\Models\Role::create(['name' => 'teacher']);
        $studentRole = \Spatie\Permission\Models\Role::create(['name' => 'student']);

        // Create admin user
        $admin = \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@lms.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($adminRole);

        // Create teacher user
        $teacher = \App\Models\User::factory()->create([
            'name' => 'Teacher',
            'email' => 'teacher@lms.com',
            'password' => bcrypt('password'),
        ]);
        $teacher->assignRole($teacherRole);

        // Seed some categories
        \App\Models\Category::create(['name' => 'Web Development', 'slug' => 'web-development']);
        \App\Models\Category::create(['name' => 'Design', 'slug' => 'design']);
    }
}
