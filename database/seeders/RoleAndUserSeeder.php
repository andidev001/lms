<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $guruRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'guru']);
        $siswaRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'siswa']);

        // Create Admin User
        $admin = \App\Models\User::firstOrCreate([
            'email' => 'admin@lms.com'
        ], [
            'name' => 'Administrator',
            'password' => bcrypt('password')
        ]);
        $admin->assignRole($adminRole);

        // Create Guru User
        $guru = \App\Models\User::firstOrCreate([
            'email' => 'guru@lms.com'
        ], [
            'name' => 'Guru Pengajar',
            'password' => bcrypt('password')
        ]);
        $guru->assignRole($guruRole);
        
        // Create Siswa User
        $siswa = \App\Models\User::firstOrCreate([
            'email' => 'siswa@lms.com'
        ], [
            'name' => 'Siswa Teladan',
            'password' => bcrypt('password')
        ]);
        $siswa->assignRole($siswaRole);
    }
}
