<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prayer_supports', function (Blueprint $table) {
    $table->id();

    $table->foreignId('prayer_request_id')
        ->constrained('prayer_requests')
        ->cascadeOnDelete();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->timestamp('prayed_at')->useCurrent();

    $table->timestamps();

    $table->unique([
        'prayer_request_id',
        'user_id'
    ]);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_supports');
    }
};
