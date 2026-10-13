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
        Schema::create('player_rankings', function (Blueprint $table) {
            $table->id();
            $table->string('gamertag');
            $table->integer('rank');
            $table->integer('rating')->default(1000);
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->integer('kills')->default(0);
            $table->integer('deaths')->default(0);
            $table->string('clan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_rankings');
    }
};
