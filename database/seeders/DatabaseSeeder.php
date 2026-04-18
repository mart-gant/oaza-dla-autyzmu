<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(['email' => 'admin@oaza.pl'], [
            'name' => 'Admin',
            'password' => Hash::make('Admin@Oaza2026!Secure#'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create test users for different roles
        User::updateOrCreate(['email' => 'jan@example.com'], [
            'name' => 'Jan Kowalski',
            'password' => Hash::make('password'),
            'role' => 'autistic_person',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'anna@example.com'], [
            'name' => 'Anna Nowak',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'maria@example.com'], [
            'name' => 'Dr Maria Wiśniewska',
            'password' => Hash::make('password'),
            'role' => 'therapist',
            'is_specialist' => true,
            'specialization' => 'Terapia behawioralna, ABA',
            'description' => 'Specjalistka z 10-letnim doświadczeniem w pracy z osobami ze spektrum autyzmu.',
            'email_verified_at' => now(),
        ]);

        $this->call(FacilitySeeder::class);
        $this->call(SpecialistSeeder::class);
        $this->call(ArticleSeeder::class);
        $this->call(ForumSeeder::class);
    }
}
