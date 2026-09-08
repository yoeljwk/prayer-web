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
        Schema::create('prayer_rooms', function (Blueprint $table) {
    $table->id();

    $table->foreignId('host_user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->foreignId('prayer_group_id')
        ->nullable()
        ->constrained('prayer_groups')
        ->nullOnDelete();

    $table->string('name', 100);
    $table->text('description')->nullable();

    $table->string('room_code', 50)->unique();

    $table->string('visibility', 20)->default('public');
    $table->string('status', 20)->default('scheduled');

    $table->integer('max_participants')->nullable();

    $table->timestamp('starts_at')->nullable();
    $table->timestamp('ends_at')->nullable();

    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_rooms');
    }
};
