<?php

use App\Models\Diagram;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('diagram.{shareToken}', function ($user, string $shareToken) {
    $diagram = Diagram::where('share_token', $shareToken)->first();

    if (!$diagram) {
        return false;
    }

    $colors = ['#E53935', '#D81B60', '#8E24AA', '#3949AB', '#1E88E5', '#00ACC1', '#43A047', '#FB8C00'];
    $color = $colors[$user->getAuthIdentifier() % count($colors)];

    return [
        'id'    => (string) $user->getAuthIdentifier(),
        'name'  => $user->email,
        'color' => $color,
    ];
});
