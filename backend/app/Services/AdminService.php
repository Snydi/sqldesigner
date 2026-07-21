<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Diagram;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use SensitiveParameter;

class AdminService
{
    public function __construct(private readonly LibraryService $libraryService) {}

    public function authenticate(string $username, #[SensitiveParameter] string $password): bool
    {
        return hash_equals('admin', $username)
            && hash_equals((string) config('app.admin_password'), $password);
    }

    /** @return array{users: LengthAwarePaginator<int, User>, totalUsers: int, registrationsByDay: array<string, int>, activityByDay: array<string, int>, returningUsers: int, retentionRate: float} */
    public function getDashboardData(string $sort = 'registered'): array
    {
        $tz = 'Europe/Moscow';
        $cutoff = now($tz)->subDays(59)->startOfDay()->utc();

        $rows = DB::table('users')
            ->selectRaw("DATE(created_at AT TIME ZONE 'UTC' AT TIME ZONE 'Europe/Moscow') as day, COUNT(*) as count")
            ->where('created_at', '>=', $cutoff)
            ->groupByRaw("DATE(created_at AT TIME ZONE 'UTC' AT TIME ZONE 'Europe/Moscow')")
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $days = [];
        for ($i = 59; $i >= 0; $i--) {
            $date = now($tz)->subDays($i)->format('Y-m-d');
            $days[$date] = $rows->has($date) ? (int) $rows[$date]->count : 0;
        }

        $activityRows = DB::table('diagram_changelog')
            ->selectRaw("DATE(created_at AT TIME ZONE 'UTC' AT TIME ZONE 'Europe/Moscow') as day, COUNT(DISTINCT user_id) as count")
            ->whereNotNull('user_id')
            ->where('created_at', '>=', $cutoff)
            ->groupByRaw("DATE(created_at AT TIME ZONE 'UTC' AT TIME ZONE 'Europe/Moscow')")
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $activityByDay = [];
        for ($i = 59; $i >= 0; $i--) {
            $date = now($tz)->subDays($i)->format('Y-m-d');
            $activityByDay[$date] = $activityRows->has($date) ? (int) $activityRows[$date]->count : 0;
        }

        $usersQuery = User::with(['diagrams', 'activeSubscription']);

        if ($sort === 'last_action') {
            $usersQuery
                ->leftJoinSub(
                    DB::table('diagram_changelog')
                        ->selectRaw('user_id, MAX(created_at) as last_action_at')
                        ->whereNotNull('user_id')
                        ->groupBy('user_id'),
                    'changelog_agg',
                    'changelog_agg.user_id', '=', 'users.id'
                )
                ->orderByRaw('changelog_agg.last_action_at DESC NULLS LAST')
                ->select('users.*');
        } else {
            $usersQuery->orderBy('created_at', 'desc');
        }

        $totalUsers = User::count();

        $returningUsers = DB::table(
            DB::table('diagram_changelog')
                ->selectRaw('user_id')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->havingRaw("COUNT(DISTINCT DATE(created_at AT TIME ZONE 'UTC' AT TIME ZONE 'Europe/Moscow')) >= 2"),
            'sub'
        )->count();

        $retentionRate = $totalUsers > 0 ? round($returningUsers / $totalUsers * 100, 1) : 0;

        return [
            'users' => $usersQuery->paginate(20)->withQueryString(),
            'totalUsers' => $totalUsers,
            'activityByDay' => $activityByDay,
            'registrationsByDay' => $days,
            'returningUsers' => $returningUsers,
            'retentionRate' => $retentionRate,
        ];
    }

    /** @return Collection<int, Diagram> */
    public function getLibraryDiagrams(): Collection
    {
        return Diagram::with('user')
            ->library()
            ->orderByDesc('featured')
            ->orderByDesc('updated_at')
            ->get();
    }

    public function featureDiagram(Diagram $diagram, ?string $url): void
    {
        $diagram->featured = true;
        $diagram->featured_url = $url;
        $diagram->save();
        $this->libraryService->invalidate();
    }

    public function unfeatureDiagram(Diagram $diagram): void
    {
        $diagram->featured = false;
        $diagram->featured_url = null;
        $diagram->save();
        $this->libraryService->invalidate();
    }

    public function impersonate(User $user): string
    {
        return $user->createToken('admin-impersonate')->plainTextToken;
    }

    /**
     * Delete a user along with all their diagrams and tokens.
     */
    public function deleteUser(User $user): void
    {
        $user->diagrams()->delete();
        $user->tokens()->delete();
        $user->delete();
    }
}
