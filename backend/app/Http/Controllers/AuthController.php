<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\Admin\NewUserRegistrationMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = new User([
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->save();
            Mail::to(config('mail.from.address'))->send(new NewUserRegistrationMail($request->email)); //TODO add this to queue, so the is no load time on register
            return response()->json([
                'status' => true,
                'message' => 'Registered successfully'
            ]);

        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Wrong email or password',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            $token = preg_replace('/(\d\|)(.+)/', '$2', $user->createToken("API TOKEN")->plainTextToken);

            return response()->json([
                'message' => 'Logged in successfully',
                'token' => $token
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        Auth::guard('web')->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
