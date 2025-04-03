<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Models\Role;
use App\Models\TenantUser;

class DatabaseService {
    public function createDatabase(string $databaseName): string {
        try {
            DB::statement("CREATE DATABASE `$databaseName`");
        } catch (\Exception $e) {
            throw new \Exception("Error al crear la base de datos: " . $e->getMessage());
        }
        return $databaseName;
    }

    public function migrateDatabase(string $databaseName): void {
        Config::set('database.connections.tenant.database', $databaseName);
        DB::purge('tenant');
        DB::reconnect('tenant');
 
        DB::beginTransaction();
        try {
            Artisan::call('migrate', ['--database' => 'tenant', '--path' => 'database/migrations/tenant']);
            $this->seedDatabase($databaseName);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Error al migrar la base de datos: " . $e->getMessage());
        }
    }

    public function createTenantUser(string $databaseName, array $userData): void {
        Config::set('database.connections.tenant.database', $databaseName);
        DB::purge('tenant');
        DB::reconnect('tenant');
 
        // Obtener el rol de administrador
        $adminRole = Role::on('tenant')->where('name', 'admin')->first();
 
        // Crear el usuario usando Eloquent
        TenantUser::on('tenant')->create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
            'role_id' => $adminRole->id,
        ]);
    }

    public function seedDatabase(string $databaseName): void {
        Config::set('database.connections.tenant.database', $databaseName);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Ejecutar el seeder de roles
        Artisan::call('db:seed', ['--database' => 'tenant', '--class' => 'RolesTableSeeder']);

        // Ejecutar el seeder de productos
        Artisan::call('db:seed', ['--database' => 'tenant', '--class' => 'ProductSeeder']);
    }
}
