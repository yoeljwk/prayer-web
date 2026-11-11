<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrayerGroup;
use App\Models\PrayerGroupMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PrayerGroupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user('sanctum')?->id;
        $search = $request->query('search');
        $visibility = $request->query('visibility');
        $joined = $request->boolean('joined');
        $notJoined = $request->boolean('not_joined');

        $query = PrayerGroup::query()
            ->withCount(['members' => function ($q) {
                $q->where('status', 'active');
            }])
            ->withCount(['prayerRequests as prayers_count' => function ($q) {
                $q->where('status', 'active');
            }])
            ->with('creator:id,name,username,avatar');

        // Security / Visibility Filter
        if ($joined && $userId) {
            $query->whereHas('members', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('status', 'active');
            });
        } elseif ($visibility && in_array($visibility, ['public', 'private'])) {
            $query->where('visibility', $visibility);
            if ($visibility === 'private' && $userId) {
                $query->whereHas('members', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('status', 'active');
                });
            }
        } else {
            // Default feed: public groups OR private groups where user is a member
            $query->where(function ($q) use ($userId) {
                $q->where('visibility', 'public');
                if ($userId) {
                    $q->orWhereHas('members', function ($mq) use ($userId) {
                        $mq->where('user_id', $userId)->where('status', 'active');
                    });
                }
            });
        }

        // Filter out groups user has already joined if not_joined=true
        if ($notJoined && $userId) {
            $query->whereDoesntHave('members', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('status', 'active');
            });
        }

        // Search Filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Check is_joined status for current Sanctum user
        if ($userId) {
            $query->withExists(['members as is_joined' => function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('status', 'active');
            }]);
        }

        $groups = $query->latest()->get()->map(function ($group) use ($userId) {
            if (!$userId || (int)$userId !== (int)$group->created_by) {
                $group->makeHidden(['invite_code']);
                $group->invite_code = null;
            }
            return $group;
        });

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'visibility' => ['required', 'in:public,private'],
            'max_members' => ['nullable', 'integer', 'min:2', 'max:1000'],
            'avatar' => ['nullable', 'string'],
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;
        while (PrayerGroup::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $group = PrayerGroup::create([
            'created_by' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'visibility' => $validated['visibility'],
            'avatar' => $validated['avatar'] ?? null,
            'invite_code' => Str::random(8),
            'max_members' => $validated['max_members'] ?? 100,
        ]);

        // Auto-add creator as owner member
        PrayerGroupMember::create([
            'prayer_group_id' => $group->id,
            'user_id' => $request->user()->id,
            'role' => 'owner',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $group->loadCount('members');
        $group->load('creator:id,name,username,avatar');
        $group->is_joined = true;

        return response()->json([
            'success' => true,
            'message' => 'Komunitas doa berhasil dibuat.',
            'data' => $group,
        ], 201);
    }

    public function show($identifier): JsonResponse
    {
        $userId = request()->user('sanctum')?->id;

        $query = PrayerGroup::query();

        if (is_numeric($identifier)) {
            $query->where('id', $identifier);
        } else {
            $query->where('slug', $identifier);
        }

        $group = $query
            ->withCount(['members' => function ($q) {
                $q->where('status', 'active');
            }])
            ->with([
                'creator:id,name,username,avatar',
                'members' => function ($q) {
                    $q->where('status', 'active')->with('user:id,name,username,avatar')->latest('joined_at');
                },
                'prayerRequests' => function ($q) use ($userId) {
                    $q->where('status', 'active')
                        ->with(['user:id,name,username,avatar', 'comments.user:id,name,username,avatar'])
                        ->withCount(['supports', 'comments'])
                        ->when($userId, function ($sq) use ($userId) {
                            $sq->withExists(['supports as has_prayed' => function ($ssq) use ($userId) {
                                $ssq->where('user_id', $userId);
                            }]);
                        })
                        ->latest();
                },
            ])
            ->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Komunitas tidak ditemukan.',
            ], 404);
        }

        if ($userId) {
            $group->is_joined = $group->members()->where('user_id', $userId)->where('status', 'active')->exists();
            $myMember = $group->members()->where('user_id', $userId)->where('status', 'active')->first();
            $group->user_role = $myMember ? $myMember->role : null;
        } else {
            $group->is_joined = false;
            $group->user_role = null;
        }

        // Hide sensitive invite code for non-creators
        if (!$userId || (int)$userId !== (int)$group->created_by) {
            $group->makeHidden(['invite_code']);
            $group->invite_code = null;
        }

        // If private group and user is not a member, hide prayer requests
        if ($group->visibility === 'private' && !$group->is_joined) {
            $group->setRelation('prayerRequests', collect([]));
        }

        return response()->json([
            'success' => true,
            'data' => $group,
        ]);
    }

    public function removeMember(Request $request, PrayerGroup $group, $userId): JsonResponse
    {
        $currentUserId = $request->user()->id;

        $myMember = PrayerGroupMember::where('prayer_group_id', $group->id)
            ->where('user_id', $currentUserId)
            ->where('status', 'active')
            ->first();

        $isOwnerOrAdmin = ($group->created_by === $currentUserId) || ($myMember && in_array($myMember->role, ['owner', 'admin']));

        if (!$isOwnerOrAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki wewenang untuk mengeluarkan anggota.',
            ], 403);
        }

        if ((int)$userId === (int)$group->created_by) {
            return response()->json([
                'success' => false,
                'message' => 'Pemilik komunitas tidak dapat dikeluarkan.',
            ], 422);
        }

        PrayerGroupMember::where('prayer_group_id', $group->id)
            ->where('user_id', $userId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anggota berhasil dikeluarkan dari komunitas.',
        ]);
    }

    public function updateMemberRole(Request $request, PrayerGroup $group, $userId): JsonResponse
    {
        $currentUserId = $request->user()->id;

        if ((int)$currentUserId !== (int)$group->created_by) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pemilik komunitas yang dapat mengubah peran anggota.',
            ], 403);
        }

        $validated = $request->validate([
            'role' => ['required', 'in:admin,member'],
        ]);

        $member = PrayerGroupMember::where('prayer_group_id', $group->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $member->update(['role' => $validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Peran anggota berhasil diperbarui.',
            'data' => $member,
        ]);
    }

    public function join(PrayerGroup $group): JsonResponse
    {
        $userId = request()->user()->id;

        $member = PrayerGroupMember::firstOrCreate(
            [
                'prayer_group_id' => $group->id,
                'user_id' => $userId,
            ],
            [
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ]
        );

        if ($member->status !== 'active') {
            $member->update(['status' => 'active', 'joined_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil bergabung dengan komunitas.',
            'data' => [
                'is_joined' => true,
                'members_count' => $group->members()->where('status', 'active')->count(),
            ],
        ]);
    }

    public function leave(PrayerGroup $group): JsonResponse
    {
        $userId = request()->user()->id;

        PrayerGroupMember::where('prayer_group_id', $group->id)
            ->where('user_id', $userId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil keluar dari komunitas.',
            'data' => [
                'is_joined' => false,
                'members_count' => $group->members()->where('status', 'active')->count(),
            ],
        ]);
    }

    public function joinByCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invite_code' => ['required', 'string'],
        ]);

        $code = trim($validated['invite_code']);

        $group = PrayerGroup::where('invite_code', $code)->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Kode undangan tidak valid atau komunitas tidak ditemukan.',
            ], 404);
        }

        $userId = $request->user()->id;

        $member = PrayerGroupMember::firstOrCreate(
            [
                'prayer_group_id' => $group->id,
                'user_id' => $userId,
            ],
            [
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ]
        );

        if ($member->status !== 'active') {
            $member->update(['status' => 'active', 'joined_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil bergabung dengan komunitas {$group->name}.",
            'data' => [
                'slug' => $group->slug,
                'group' => $group,
            ],
        ]);
    }

    public function update(Request $request, PrayerGroup $group): JsonResponse
    {
        if ($request->user()->id !== $group->created_by) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengedit komunitas ini.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string', 'max:1000'],
            'visibility' => ['sometimes', 'required', 'in:public,private'],
            'max_members' => ['nullable', 'integer', 'min:2', 'max:1000'],
            'avatar' => ['nullable', 'string'],
        ]);

        if (isset($validated['name']) && $validated['name'] !== $group->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $count = 1;
            while (PrayerGroup::where('slug', $slug)->where('id', '!=', $group->id)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }
            $validated['slug'] = $slug;
        }

        $group->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Komunitas doa berhasil diperbarui.',
            'data' => $group->fresh(),
        ]);
    }

    public function destroy(PrayerGroup $group): JsonResponse
    {
        if (request()->user()->id !== $group->created_by) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus komunitas ini.',
            ], 403);
        }

        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komunitas doa berhasil dihapus.',
        ]);
    }
}
