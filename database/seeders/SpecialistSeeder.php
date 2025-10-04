<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class SpecialistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialists = User::where('is_specialist', true)->take(2)->get();

        if ($specialists->count() > 0) {
            $specialists[0]->update([
                'specialization' => 'Psycholog', 
                'description' => 'Specjalizuję się w terapii poznawczo-behawioralnej. Pomagam osobom zmagającym się z lękiem i depresją.'
            ]);
        }

        if ($specialists->count() > 1) {
            $specialists[1]->update([
                'specialization' => 'Dietetyk', 
                'description' => 'Pomagam w układaniu zdrowych i zbilansowanych diet. Skupiam się na zmianie nawyków żywieniowych.'
            ]);
        }
    }
}
