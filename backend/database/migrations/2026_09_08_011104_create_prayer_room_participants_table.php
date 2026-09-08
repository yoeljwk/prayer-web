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
        Schema::create('prayer_room_participants', function (Blueprint $table) {
    $table->id();

    $table->foreignId('prayer_room_id')
        ->constrained('prayer_rooms')
        ->cascadeOnDelete();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('role', 20)->default('participant');

    $table->timestamp('joined_at')->nullable();
    $table->timestamp('left_at')->nullable();

    $table->timestamps();

    $table->unique([
        'prayer_room_id',
        'user_id'
    ]);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_room_participants');
    }
};
