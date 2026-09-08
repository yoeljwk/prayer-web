<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use App\Models\PrayerSupport;
use Illuminate\Http\JsonResponse;

class PrayerSupportController extends Controller
{
    public function store(
        PrayerRequest $prayer
    ): JsonResponse {
        $support = PrayerSupport::firstOrCreate(
            [
                'prayer_request_id' => $prayer->id,
                'user_id' => request()->user()->id,
            ],
            [
                'prayed_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $support->wasRecentlyCreated
                ? 'Terima kasih telah mendoakan.'
                : 'Anda sudah mendoakan permohonan ini.',
            'data' => [
                'supported' => true,
                'supports_count' =>
                    $prayer->supports()->count(),
            ],
        ]);
    }

    public function destroy(
        PrayerRequest $prayer
    ): JsonResponse {
        PrayerSupport::query()
            ->where('prayer_request_id', $prayer->id)
            ->where('user_id', request()->user()->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dukungan doa dibatalkan.',
            'data' => [
                'supported' => false,
                'supports_count' =>
                    $prayer->supports()->count(),
            ],
        ]);
    }
}