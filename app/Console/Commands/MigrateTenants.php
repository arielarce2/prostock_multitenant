<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\User; // O el modelo que gestiona tenants

class MigrateTenants extends Command
{
    protected $signature = 'tenants:migrate';
    protected $description = 'Ejecuta migraciones en todas las bases de datos de los tenants';

    public function handle()
    {
        $tenants = User::whereNotNull('tenant_db')->pluck('tenant_db');

        foreach ($tenants as $tenant) {
            Config::set('database.connections.tenant.database', $tenant);
            DB::purge('tenant');
            DB::reconnect('tenant');

            $this->info("Ejecutando migraciones en la base de datos: $tenant");
            $this->call('migrate', ['--database' => 'tenant', '--path' => 'database/migrations/tenant']);
        }

        $this->info('Migraciones completadas para todos los tenants.');
    }
}
