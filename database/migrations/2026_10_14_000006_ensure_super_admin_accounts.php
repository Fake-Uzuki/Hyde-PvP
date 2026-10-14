<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure default Super Admin account exists with known credentials
        User::updateOrCreate(
            ['email' => 'superadmin@hydepvp.com'],
            [
                'name' => 'HYDE Super Admin',
                'password' => Hash::make('HydeSuperAdmin2026!'),
                'role' => 'super_admin',
                'minecraft_gamertag' => 'HydeOwner',
                'email_verified_at' => now(),
            ]
        );

        // 2. Ensure owner account tdarklight@gmail.com is super_admin and purge any duplicate
        User::where('email', 'tdarklight836@gmail.com')->delete();
        User::where('email', 'tdarklight@gmail.com')
            ->update([
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
