<?php

namespace App\Services;

use App\Models\Diagram;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminService
{
    public function authenticate(string $username, string $password): bool
    {
        return hash_equals('admin', $username)
            && hash_equals((string)config('app.admin_password'), $password);
    }

    /** @return array{users: Collection, libraryDiagrams: Collection} */
    public function getDashboardData(): array
    {
        return [
            'users' => User::with('diagrams')->orderBy('created_at', 'desc')->get(),
            'libraryDiagrams' => Diagram::with('user')
                ->where('library', true)
                ->whereNotNull('share_access')
                ->orderByDesc('featured')
                ->orderByDesc('updated_at')
                ->get(),
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
