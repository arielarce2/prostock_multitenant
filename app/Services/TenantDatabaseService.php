<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Models\Role;

class TenantDatabaseService {
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

        Artisan::call('migrate', ['--database' => 'tenant', '--path' => 'database/migrations/tenant']);

        $this->seedDatabase($databaseName);
    }

    public function createTenantUser(string $databaseName, array $userData): void {
        Config::set('database.connections.tenant.database', $databaseName);
        DB::purge('tenant');
        DB::reconnect('tenant');

        $adminRole = DB::connection('tenant')->table('roles')->where('name', 'admin')->first();

        DB::connection('tenant')->table('users')->insert([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'role_id' => $adminRole->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function seedDatabase(string $databaseName): void {
        Config::set('database.connections.tenant.database', $databaseName);
        DB::purge('tenant');
        DB::reconnect('tenant');

        Artisan::call('db:seed', ['--database' => 'tenant', '--class' => 'RolesTableSeeder']);
    }
}
