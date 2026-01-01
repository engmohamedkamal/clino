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
    
        User::factory()->create([
            'name' => 'Mostafa Elfiky',
            'phone' => '01229622363',
            'id_number' => '30311141800795',
            'role' => 'admin'
        ]);
    }
}
