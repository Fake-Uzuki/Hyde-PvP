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
        Schema::table('player_rankings', function (Blueprint $table) {
            $table->index('gamertag');
            $table->index('rank');
            $table->index('rating');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('minecraft_gamertag');
        });

        Schema::table('news_posts', function (Blueprint $table) {
            $table->index(['is_published', 'published_at']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index(['news_post_id', 'is_hidden', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_rankings', function (Blueprint $table) {
            $table->dropIndex(['gamertag']);
            $table->dropIndex(['rank']);
            $table->dropIndex(['rating']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['minecraft_gamertag']);
        });

        Schema::table('news_posts', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'published_at']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['news_post_id', 'is_hidden', 'created_at']);
        });
    }
};
