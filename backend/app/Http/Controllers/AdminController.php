<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AdminLoginRequest;
use App\Models\Diagram;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Knuckles\Scribe\Attributes\Group;

#[Group("Admin")]
class AdminController extends Controller
{
    public function __construct(private readonly AdminService $adminService) {}

    public function showLogin(): View|Factory|Redirector|RedirectResponse
    {
        if (session('admin_authenticated')) {
            return redirect('/admin');
        }

        return view('admin.login');
    }

    public function login(AdminLoginRequest $request): Redirector|RedirectResponse
    {
        if ($this->adminService->authenticate($request->input('username'), $request->input('password'))) {
            session(['admin_authenticated' => true]);
            return redirect('/admin');
        }

        return back()->withErrors(['credentials' => 'Неверный логин или пароль.']);
    }

    public function showDashboard(): Factory|View
    {
        ['users' => $users, 'libraryDiagrams' => $libraryDiagrams] = $this->adminService->getDashboardData();

        return view('admin.dashboard', compact('users', 'libraryDiagrams'));
    }

    public function featureDiagram(Diagram $diagram, Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url|max:500']);

        $this->adminService->featureDiagram($diagram, $request->input('url'));

        return response()->json(['ok' => true]);
    }

    public function unfeatureDiagram(Diagram $diagram): JsonResponse
    {
        $this->adminService->unfeatureDiagram($diagram);

        return response()->json(['ok' => true]);
    }

    public function impersonate(User $user): JsonResponse
    {
        return response()->json(['token' => $this->adminService->impersonate($user)]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->adminService->deleteUser($user);

        return response()->json(['message' => 'User deleted']);
    }

    public function logout(): Redirector|RedirectResponse
    {
        session()->forget('admin_authenticated');

        return redirect('/admin/login');
    }
}
