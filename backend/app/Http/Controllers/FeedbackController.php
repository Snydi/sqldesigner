<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Jobs\SendFeedbackEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class FeedbackController extends Controller
{
    public function send(FeedbackRequest $request): JsonResponse
    {
        $data = $request->validated();

        SendFeedbackEmail::dispatch($data['message'], $data['email'] ?? null);

        return response()->json(['status' => true]);
    }
}
