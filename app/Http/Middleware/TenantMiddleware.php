<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantMiddleware {
    public function handle($request, Closure $next) {
        if (Auth::check() && Auth::user()->tenant_db) {
            Config::set('database.connections.tenant.database', Auth::user()->tenant_db);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }
        return $next($request);
    }
}