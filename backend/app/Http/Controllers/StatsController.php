<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Diagram;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        $users = Cache::remember('stats:users', 3600, fn () => User::count());
        $diagrams = Cache::remember('stats:diagrams', 3600, fn () => Diagram::count());
        $online = User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
        $stars = Cache::remember('stats:github_stars', 21600, function () {
            try {
                return Http::withHeaders(['User-Agent' => 'sql-designer.com'])
                    ->get('https://api.github.com/repos/Snydi/sqldesigner')
                    ->json('stargazers_count');
            } catch (\Throwable) {
                return null;
            }
        });

        return response()->json(compact('users', 'diagrams', 'online', 'stars'));
    }
}
