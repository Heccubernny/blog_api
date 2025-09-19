<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $token = Auth::attempt([
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            return $this->successResponse([
                'user'  => new UserResource($user),
                'token' => $token,
                'type'  => 'bearer',
            ], 'User registered successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed', 500, $e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (! $token = JWTAuth::attempt($credentials)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        return $this->successResponse([
            'user'  => new UserResource(Auth::user()),
            'token' => $token,
            'type'  => 'bearer',
        ], 'Login successful');
    }

    public function user()
    {
        return $this->successResponse(new UserResource(Auth::user()), 'Current user');
    }

    public function logout()
    {
        try {
            Auth::logout();
            return $this->successResponse(null, 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed', 500, $e->getMessage());
        }
    }

}
