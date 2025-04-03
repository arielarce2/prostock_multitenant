<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegistered;
use App\Mail\VerificationEmail;

class AuthController extends Controller {
    protected $databaseService;

    public function __construct(DatabaseService $databaseService) {
        $this->databaseService = $databaseService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        // Generar un código de verificación
        $verificationCode = Str::random(6);
    
        // Crear un usuario temporal en la base de datos central
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
        ]);
    
        // Enviar el correo de verificación
        Mail::to($user->email)->send(new VerificationEmail($user));
    
        return response()->json(['message' => 'Se ha enviado un código de verificación a tu correo.'], 201);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string',
        ]);
    
        // Buscar el usuario solo por el código de verificación
        $user = User::where('verification_code', $request->verification_code)->first();
    
        if (!$user) {
            return response()->json(['message' => 'Código de verificación inválido.'], 404);
        }

        $databaseName = 'prostock_tenant_' . Str::random(6);
        $user->email_verified_at = now();
        $user->tenant_db = $databaseName;
        $user->save();
    
        // Crear base de datos con el UUID
        $this->databaseService->createDatabase($user->tenant_db);
    
        // Ejecutar migraciones en la nueva base de datos
        $this->databaseService->migrateDatabase($user->tenant_db);
    
        // Crear usuario en la base de datos del tenant
        $this->databaseService->createTenantUser($user->tenant_db, [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ]);

        Mail::to($user->email)->send(new UserRegistered($user));
    
        return response()->json(['message' => 'Cuenta creada exitosamente.'], 201);
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

