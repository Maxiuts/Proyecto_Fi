<?php

namespace Database\Seeders;

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
        $this->call([
            UserSeeder::class,    // primero usuarios (admin + clientes de prueba)
            CategorySeeder::class,
            TagSeeder::class,
            ProductSeeder::class, // depende de categories y tags
        ]);
    }
}
