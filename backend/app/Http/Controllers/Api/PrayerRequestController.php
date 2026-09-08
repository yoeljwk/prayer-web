<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrayerRequest;
use App\Http\Requests\UpdatePrayerRequest;
use App\Models\PrayerRequest;
use Illuminate\Http\JsonResponse;

class PrayerRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $prayers = PrayerRequest::query()
            ->with([
                'user:id,name,username,avatar',
                'group:id,name,slug',
            ])
            ->withCount([
                'supports',
                'comments',
            ])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan doa berhasil diambil.',
            'data' => $prayers,
        ]);
    }

    public function store(
        StorePrayerRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $data['user_id'] = $request->user()->id;

        if ($data['visibility'] !== 'group') {
            $data['prayer_group_id'] = null;
        }

        $prayer = PrayerRequest::create($data);

        $prayer->load([
            'user:id,name,username,avatar',
            'group:id,name,slug',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan doa berhasil dibagikan.',
            'data' => $prayer,
        ], 201);
    }

    public function show(
        PrayerRequest $prayer
    ): JsonResponse {
        $prayer->load([
            'user:id,name,username,avatar',
            'group:id,name,slug',
            'comments.user:id,name,username,avatar',
        ]);

        $prayer->loadCount([
            'supports',
            'comments',
        ]);

        return response()->json([
            'success' => true,
            'data' => $prayer,
        ]);
    }

    public function update(
        UpdatePrayerRequest $request,
        PrayerRequest $prayer
    ): JsonResponse {
        if ($request->user()->id !== $prayer->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses.',
            ], 403);
        }

        $data = $request->validated();

        if (
            isset($data['visibility']) &&
            $data['visibility'] !== 'group'
        ) {
            $data['prayer_group_id'] = null;
        }

        $prayer->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan doa berhasil diperbarui.',
            'data' => $prayer->fresh(),
        ]);
    }

    public function destroy(
        PrayerRequest $prayer
    ): JsonResponse {
        if (request()->user()->id !== $prayer->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses.',
            ], 403);
        }

        $prayer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permohonan doa berhasil dihapus.',
        ]);
    }
}