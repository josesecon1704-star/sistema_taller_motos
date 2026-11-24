<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            PermisoSeeder::class,
            EmpleadoSeeder::class,
            UserSeeder::class,
            ServicioSeeder::class,
            ProveedorSeeder::class,
            RepuestoSeeder::class,
        ]);

        // Asegurar siempre un acceso administrador garantizado al terminar
        User::updateOrCreate(
            ['email' => 'admin@taller.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password123')
            ]
        );
    }
}
