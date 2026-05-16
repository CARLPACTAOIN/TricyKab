<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private readonly AdminNotificationService $notifications,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $payload = $this->notifications->forUser($request->user());

        return response()->json($payload);
    }

    public function dismiss(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|max:64',
        ]);

        $this->notifications->dismiss($request->user(), $validated['key']);

        return response()->json($this->notifications->forUser($request->user()));
    }
}
