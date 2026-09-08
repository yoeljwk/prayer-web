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
        Schema::create('prayer_requests', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('prayer_group_id')
        ->nullable()
        ->constrained('prayer_groups')
        ->nullOnDelete();

    $table->text('content');

    $table->string('visibility', 20)->default('public');
    $table->boolean('is_anonymous')->default(false);

    $table->string('status', 20)->default('active');
    $table->timestamp('answered_at')->nullable();

    $table->timestamps();
    $table->softDeletes();

    $table->index('status');
    $table->index('visibility');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_requests');
    }
};
