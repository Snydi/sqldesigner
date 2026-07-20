<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AdminSendEmailRequest;
use App\Http\Requests\Admin\FeatureDiagramRequest;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Jobs\SendAdminBulkEmailBatch;
use App\Mail\AdminEmailMail;
use App\Models\Diagram;
use App\Models\ExportUsage;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Knuckles\Scribe\Attributes\Group;

#[Group('Admin')]
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
        $sort = in_array(request('sort'), ['registered', 'last_action']) ? request('sort') : 'registered';
        ['users' => $users, 'totalUsers' => $totalUsers, 'registrationsByDay' => $registrationsByDay, 'activityByDay' => $activityByDay, 'returningUsers' => $returningUsers, 'retentionRate' => $retentionRate] = $this->adminService->getDashboardData($sort);

        return view('admin.dashboard', compact('users', 'totalUsers', 'registrationsByDay', 'activityByDay', 'sort', 'returningUsers', 'retentionRate'));
    }

    public function showLibrary(): Factory|View
    {
        $libraryDiagrams = $this->adminService->getLibraryDiagrams();

        return view('admin.library', compact('libraryDiagrams'));
    }

    public function showBilling(): Factory|View
    {
        $activeProUsers = Subscription::query()->providingProAccess()->distinct('user_id')->count('user_id');
        $successfulPayments = Payment::query()->where('status', 'succeeded')->count();
        $pendingPayments = Payment::query()->where('status', 'initiated')->count();
        $nominalRevenueMinor = (int) Payment::query()->where('status', 'succeeded')->sum('amount_minor');
        $subscriptions = Subscription::with('user')->latest()->limit(50)->get();
        $payments = Payment::with('user')->latest()->limit(50)->get();
        $webhookLogs = PaymentWebhookLog::with('payment.user')->latest()->limit(50)->get();
        $exportUsages = ExportUsage::with('user')
            ->where('usage_date', now('Europe/Moscow')->toDateString())
            ->orderByDesc('count')
            ->limit(50)
            ->get();

        return view('admin.billing', compact(
            'activeProUsers',
            'successfulPayments',
            'pendingPayments',
            'nominalRevenueMinor',
            'subscriptions',
            'payments',
            'webhookLogs',
            'exportUsages',
        ));
    }

    public function featureDiagram(Diagram $diagram, FeatureDiagramRequest $request): JsonResponse
    {
        $this->adminService->featureDiagram($diagram, $request->input('url') ?: null);

        return $this->success(['ok' => true]);
    }

    public function unfeatureDiagram(Diagram $diagram): JsonResponse
    {
        $this->adminService->unfeatureDiagram($diagram);

        return $this->noContent();
    }

    public function impersonate(User $user): JsonResponse
    {
        return $this->success(['token' => $this->adminService->impersonate($user)]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->adminService->deleteUser($user);

        return $this->noContent();
    }

    public function sendEmailToAll(AdminSendEmailRequest $request): JsonResponse
    {
        $subject = $request->input('subject');
        $body = $request->input('body');

        $count = User::count();
        SendAdminBulkEmailBatch::dispatch($subject, $body);

        return $this->success(['queued' => $count]);
    }

    public function sendEmail(User $user, AdminSendEmailRequest $request): JsonResponse
    {
        Mail::to($user->email)->send(new AdminEmailMail($request->input('subject'), $request->input('body')));

        return $this->success(['ok' => true]);
    }

    public function userActivity(User $user): JsonResponse
    {
        $rows = DB::table('diagram_changelog')
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(59)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $days = [];
        for ($i = 59; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $days[$date] = $rows->has($date) ? (int) $rows[$date]->count : 0;
        }

        return $this->success($days);
    }

    public function showReviews(): Factory|View
    {
        $reviews = Review::with('user')->latest()->get();

        return view('admin.reviews', compact('reviews'));
    }

    public function logout(): Redirector|RedirectResponse
    {
        session()->forget('admin_authenticated');

        return redirect('/admin/login');
    }
}
