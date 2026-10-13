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
        // 1. Add verification status columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('minecraft_gamertag');
            $table->string('xuid', 32)->nullable()->after('is_verified');
            $table->timestamp('verified_at')->nullable()->after('xuid');
        });

        // 2. Create in-game verification codes table
        Schema::create('player_verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code', 16)->unique();
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->timestamps();

            $table->index(['code', 'is_used', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_verification_codes');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'xuid', 'verified_at']);
        });
    }
};
