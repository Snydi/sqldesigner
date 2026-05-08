<?php

namespace App\Services;

use App\Models\Diagram;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AdminService
{
    public function authenticate(string $username, string $password): bool
    {
        return hash_equals('admin', $username)
            && hash_equals((string)config('app.admin_password'), $password);
    }

    /** @return array{users: Collection, libraryDiagrams: Collection, registrationsByDay: array} */
    public function getDashboardData(): array
    {
        $rows = DB::table('users')
            ->selectRaw("DATE(created_at) as day, COUNT(*) as count")
            ->where('created_at', '>=', now()->subDays(59)->startOfDay())
            ->groupByRaw("DATE(created_at)")
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $days = [];
        for ($i = 59; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $days[$date] = $rows->has($date) ? (int) $rows[$date]->count : 0;
        }

        return [
            'users' => User::with('diagrams')->orderBy('created_at', 'desc')->get(),
            'libraryDiagrams' => Diagram::with('user')
                ->where('library', true)
                ->whereNotNull('share_access')
                ->orderByDesc('featured')
                ->orderByDesc('updated_at')
                ->get(),
            'registrationsByDay' => $days,
        ];
    }

    public function featureDiagram(Diagram $diagram, string $url): void
    {
        $diagram->featured = true;
        $diagram->featured_url = $url;
        $diagram->save();
    }

    public function unfeatureDiagram(Diagram $diagram): void
    {
        $diagram->featured = false;
        $diagram->featured_url = null;
        $diagram->save();
    }

    public function impersonate(User $user): string
    {
        return $user->createToken('admin-impersonate')->plainTextToken;
    }

    public function deleteUser(User $user): void
    {
        $user->diagrams()->delete();
        $user->tokens()->delete();
        $user->delete();
    }
}
