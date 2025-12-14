<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Post /register
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registro exitoso',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    //Post /login
    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        //buscar el usuario 
        $user = User::where('email', $request->email)->first();
        //verificar la contraseña
        if (!$user || !\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        //crear token
        //el token tendra el nombre del rol para identificarlo facilmente
        $tokenName = $user->createToken($user->role)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'user' => $user,
                'token' => $tokenName
            ]
        ], 200);
    }

    //Post /logout
    public function logout(Request $request){
        //eliminar todos los tokens del usuario autenticado
        $request->user()->currencetAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cierre de sesión exitoso'
        ], 200);
} 
}