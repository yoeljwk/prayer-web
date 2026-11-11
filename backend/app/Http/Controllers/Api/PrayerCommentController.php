<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrayerCommentController extends Controller
{
    public function index(PrayerRequest $prayer): JsonResponse
    {
        $comments = $prayer->comments()
            ->with('user:id,name,username,avatar')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comments,
        ]);
    }

    public function store(Request $request, PrayerRequest $prayer): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        $comment = $prayer->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'is_anonymous' => $validated['is_anonymous'] ?? false,
        ]);

        $comment->load('user:id,name,username,avatar');

        return response()->json([
            'success' => true,
            'message' => 'Pesan penguatan berhasil dikirim.',
            'data' => $comment,
        ], 201);
    }
}
