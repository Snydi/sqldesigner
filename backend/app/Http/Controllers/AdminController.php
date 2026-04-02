<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (session('admin_authenticated')) {
            return redirect('/admin');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (
            hash_equals('admin', $request->input('username')) &&
            hash_equals((string) config('app.admin_password'), $request->input('password'))
        ) {
            session(['admin_authenticated' => true]);
            return redirect('/admin');
        }

        return back()->withErrors(['credentials' => 'Неверный логин или пароль.']);
    }

    public function showDashboard()
    {
        $users = User::with('diagrams')->orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('users'));
    }

    public function impersonate(User $user)
    {
        $token = $user->createToken('admin-impersonate')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->diagrams()->delete();
        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }

    public function logout(Request $request)
    {
        session()->forget('admin_authenticated');

        return redirect('/admin/login');
    }
}
