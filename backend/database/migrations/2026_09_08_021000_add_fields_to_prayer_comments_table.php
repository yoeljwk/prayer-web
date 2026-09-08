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
        Schema::table('prayer_comments', function (Blueprint $table) {
            $table->foreignId('prayer_request_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->after('prayer_request_id')->constrained()->cascadeOnDelete();
            $table->text('content')->after('user_id');
            $table->boolean('is_anonymous')->default(false)->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prayer_comments', function (Blueprint $table) {
            $table->dropForeign(['prayer_request_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['prayer_request_id', 'user_id', 'content', 'is_anonymous']);
        });
    }
};
