<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AdminSendEmailRequest;
use App\Http\Requests\Admin\FeatureDiagramRequest;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Jobs\SendAdminBulkEmail;
use App\Mail\AdminEmailMail;
use App\Models\Diagram;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Mail;
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

    public function featureDiagram(Diagram $diagram, FeatureDiagramRequest $request): JsonResponse
    {
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

    public function sendEmailToAll(AdminSendEmailRequest $request): JsonResponse
    {
        $subject = $request->input('subject');
        $body    = $request->input('body');

        $emails = User::pluck('email');

        foreach ($emails as $index => $email) {
            SendAdminBulkEmail::dispatch($email, $subject, $body)
                ->delay(now()->addSeconds($index * 2));
        }

        return response()->json(['queued' => $emails->count()]);
    }

    public function sendEmail(User $user, AdminSendEmailRequest $request): JsonResponse
    {
        Mail::to($user->email)->send(new AdminEmailMail($request->input('subject'), $request->input('body')));

        return response()->json(['ok' => true]);
    }

    public function logout(): Redirector|RedirectResponse
    {
        session()->forget('admin_authenticated');

        return redirect('/admin/login');
    }
}
