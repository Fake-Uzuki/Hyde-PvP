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
        Schema::create('clan_rankings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tag', 10);
            $table->integer('rank');
            $table->string('leader');
            $table->integer('score')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('members_count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clan_rankings');
    }
};
