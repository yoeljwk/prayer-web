<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrayerRequest;
use App\Http\Requests\UpdatePrayerRequest;
use App\Models\PrayerRequest;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;
use App\Models\PrayerGroupMember;

class PrayerRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user('sanctum')?->id;
        $type = $request->query('type', 'public');

        $query = PrayerRequest::query()
            ->where('status', 'active');

        if ($type === 'community') {
            if (!$userId) {
                return response()->json([
                    'success' => true,
                    'message' => 'Silakan login untuk melihat doa komunitas.',
                    'data' => [
                        'current_page' => 1,
                        'data' => [],
                        'last_page' => 1,
                        'total' => 0,
                    ],
                ]);
            }

            $userGroupIds = PrayerGroupMember::where('user_id', $userId)
                ->where('status', 'active')
                ->pluck('prayer_group_id');

            $query->where('visibility', 'group');

            if ($groupId = $request->query('group_id')) {
                $query->where('prayer_group_id', $groupId)
                    ->whereIn('prayer_group_id', $userGroupIds);
            } else {
                $query->whereIn('prayer_group_id', $userGroupIds);
            }
        } else {
            $query->where('visibility', 'public');
        }

        // Server-Side Search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    })
                    ->orWhereHas('group', function ($gq) use ($search) {
                        $gq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Server-Side Date Filter
        if ($filter = $request->query('filter')) {
            if ($filter === 'today') {
                $query->where('created_at', '>=', now()->startOfDay());
            } elseif ($filter === '3days') {
                $query->where('created_at', '>=', now()->subDays(3)->startOfDay());
            } elseif ($filter === 'thisweek') {
                $query->where('created_at', '>=', now()->startOfWeek());
            }
        }

        $prayers = $query->with([
                'user:id,name,username,avatar',
                'group:id,name,slug',
                'comments.user:id,name,username,avatar',
            ])
            ->withCount([
                'supports',
                'comments',
            ])
            ->when($userId, function ($q) use ($userId) {
                $q->withExists([
                    'supports as has_prayed' => function ($sq) use ($userId) {
                        $sq->where('user_id', $userId);
                    },
                ]);
            })
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

        $userId = request()->user('sanctum')?->id;
        if ($userId) {
            $prayer->loadExists([
                'supports as has_prayed' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                },
            ]);
        }

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

        $prayer->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Permohonan doa berhasil dihapus.',
        ]);
    }

    public function myPrayers(): JsonResponse
    {
        $user = request()->user();
        $prayers = PrayerRequest::where('user_id', $user->id)
            ->with([
                'group:id,name,slug',
                'comments.user:id,name,username,avatar',
            ])
            ->withCount(['supports', 'comments'])
            ->withExists([
                'supports as has_prayed' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                },
            ])
            ->latest()
            ->get();

        $totalPrayers = $prayers->count();
        $answeredPrayers = $prayers->where('status', 'answered')->count();
        $totalSupportsReceived = $prayers->sum('supports_count');

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_prayers' => $totalPrayers,
                    'answered_prayers' => $answeredPrayers,
                    'total_supports_received' => $totalSupportsReceived,
                ],
                'prayers' => $prayers,
            ],
        ]);
    }
}