<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TenantDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    protected $databaseService;

    public function __construct(TenantDatabaseService $databaseService) {
        $this->databaseService = $databaseService;
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        $databaseName = 'prostock_tenant_' . Str::random(8);
    
        // Crear usuario en la base central con el UUID
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_db' => $databaseName,
        ]);
    
        // Crear base de datos con el UUID
        $this->databaseService->createDatabase($databaseName);
    
        // Ejecutar migraciones en la nueva base de datos
        $this->databaseService->migrateDatabase($databaseName);
    
        // Crear usuario en la base de datos del tenant
        $this->databaseService->createTenantUser($databaseName, [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ]);
    
        return response()->json(['message' => 'Usuario registrado y base de datos creada'], 201);
    }

    // Método para iniciar sesión
    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken($user->email);

            return response()->json([
                'message' => 'Inicio de sesión exitoso', 
                'token' => $token->plainTextToken
            ]);
        }

        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    // Método para cerrar sesión
    public function logout(Request $request) {
        // Obtener el usuario autenticado
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Obtener el token actual
        $currentToken = $user->currentAccessToken();

        if (!$currentToken) {
            return response()->json(['message' => 'Token no encontrado'], 404);
        }

        // Revocar solo el token actual
        $currentToken->delete();

        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }
}

