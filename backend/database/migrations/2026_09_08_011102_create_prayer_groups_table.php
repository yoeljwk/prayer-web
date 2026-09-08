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
        Schema::create('prayer_groups', function (Blueprint $table) {
    $table->id();

    $table->foreignId('created_by')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->string('name', 100);
    $table->string('slug', 120)->unique();

    $table->text('description')->nullable();
    $table->string('avatar')->nullable();

    $table->string('visibility', 20)->default('private');

    $table->string('invite_code', 50)->nullable()->unique();
    $table->integer('max_members')->nullable();

    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_groups');
    }
};
