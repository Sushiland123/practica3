<?php

namespace App\Http\Controllers;

    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use App\Http\Requests\Auth\RegisterRequest;
    use App\Http\Requests\Auth\LoginRequest;

    class AuthController extends Controller
    {
        public function register(RegisterRequest $request)
        {
            $user = User::create([
                'nombre' => $request->nombre,
                'correo' => $request->correo,
                'nombreUsuario' => $request->nombreUsuario,
                'edad' => $request->edad,
                'país' => $request->país,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['token' => $token], 201);
        }

        public function login(LoginRequest $request)
        {
            if (!Auth::attempt($request->only('correo', 'password'))) {
                return response()->json(['message' => 'Credenciales inválidas'], 401);
            }

            $user = User::where('correo', $request->correo)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['token' => $token], 200);
        }

        public function logout(Request $request)
        {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
        }
    }

