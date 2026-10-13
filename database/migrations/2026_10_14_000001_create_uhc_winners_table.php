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
        Schema::create('uhc_winners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('winner_gamertag');
            $table->string('runner_up_gamertag')->nullable();
            $table->integer('winner_kills')->default(0);
            $table->date('event_date');
            $table->string('game_mode')->default('Solo');
            $table->string('prize')->nullable();
            $table->string('recap_url')->nullable();
            $table->timestamps();

            $table->index('winner_gamertag');
            $table->index('event_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uhc_winners');
    }
};
