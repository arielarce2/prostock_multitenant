<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User; // O el modelo que gestiona tenants

class FreshTenants extends Command
{
    protected $signature = 'tenants:fresh';
    protected $description = 'Elimina todas las bases de datos de los tenants y ejecuta migrate:fresh';

    public function handle()
    {
        $this->info('Obteniendo bases de datos de tenants...');

        // Obtener bases de datos de los tenants
        $tenantDatabases = User::whereNotNull('tenant_db')->pluck('tenant_db');

        if ($tenantDatabases->isEmpty()) {
            $this->warn('No hay bases de datos de tenants para eliminar.');
        } else {
            foreach ($tenantDatabases as $dbName) {
                try {
                    DB::statement("DROP DATABASE IF EXISTS `$dbName`");
                    $this->info("Base de datos eliminada: $dbName");
                } catch (\Exception $e) {
                    $this->error("Error eliminando $dbName: " . $e->getMessage());
                }
            }
        }

        // Ejecutar migrate:fresh en la base de datos central
        $this->info('Ejecutando migrate:fresh en la base de datos principal...');
        $this->call('migrate:fresh');

        $this->info('Proceso completado.');
    }
}
