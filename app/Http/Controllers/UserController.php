<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TenantUser;
use Illuminate\Http\Request;

class UserController extends Controller {

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);
    
        $tenantUser = TenantUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
        ]);
    
        return response()->json(['message' => 'Usuario registrado exitosamente'], 201);
    }

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
