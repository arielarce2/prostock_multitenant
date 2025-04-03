<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TenantUser;
use Illuminate\Http\Request;

class UserController extends Controller {

    // Método para obtener información del tenant
    public function profile(Request $request) {
        try {
            $tenantUsers = TenantUser::on('tenant')->get();
            return response()->json(['profile' => $tenantUsers]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener el perfil: ' . $e->getMessage()], 500);
        }
    }
    
}
