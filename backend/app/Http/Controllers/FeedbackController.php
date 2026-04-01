<?php

namespace App\Http\Controllers;

use App\Jobs\SendFeedbackEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FeedbackController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => 'required|string|max:5000',
            'email'   => 'nullable|email|max:255',
        ]);

        SendFeedbackEmail::dispatch($data['message'], $data['email'] ?? null);

        return response()->json(['status' => true]);
    }
}
