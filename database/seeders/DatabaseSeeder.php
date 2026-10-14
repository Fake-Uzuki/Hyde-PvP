<?php

namespace Database\Seeders;

use App\Models\NewsPost;
use App\Models\Rule;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Site Settings
        foreach (SiteSetting::defaults() as $key => $val) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $val]);
        }

        // Create Super Administrator (Supreme authority)
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@hydepvp.com'],
            [
                'name' => 'HYDE Super Admin',
                'password' => Hash::make('HydeSuperAdmin2026!'),
                'role' => 'super_admin',
                'minecraft_gamertag' => 'HydeOwner',
                'email_verified_at' => now(),
            ]
        );

        // Ensure user Uzuki (if exists) is also super_admin
        User::where('email', 'tdarklight836@gmail.com')->update(['role' => 'super_admin']);

        // Create initial Administrator account
        $admin = User::firstOrCreate(
            ['email' => 'admin@hydepvp.net'],
            [
                'name' => 'Hyde Admin',
                'password' => Hash::make('AdminPassword123!'),
                'role' => 'admin',
                'minecraft_gamertag' => 'HydeMaster',
                'email_verified_at' => now(),
            ]
        );

        // Create sample staff / moderator
        $mod = User::firstOrCreate(
            ['email' => 'moderator@hydepvp.net'],
            [
                'name' => 'Hyde Moderator',
                'password' => Hash::make('ModPassword123!'),
                'role' => 'moderator',
                'minecraft_gamertag' => 'HydeGuard',
                'email_verified_at' => now(),
            ]
        );

        // Seed Default Server Rules
        $defaultRules = [
            [
                'category' => 'General Conduct',
                'title' => 'Fair Play & Respect',
                'description' => 'Treat all players with respect. Harassment, hate speech, doxxing, toxic spam, and discrimination will result in an immediate and permanent ban.',
                'order' => 1,
            ],
            [
                'category' => 'Gameplay & PvP',
                'title' => 'No Cheating, Unfair Clients, or Auto-Clickers',
                'description' => 'Any use of modified clients (Toolbox, Horion, Zephyr, etc.), auto-clickers, macros, reach modifications, velocity hacks, or unfair packet manipulation is strictly forbidden.',
                'order' => 2,
            ],
            [
                'category' => 'Gameplay & PvP',
                'title' => 'No Glitch Abuse or Bug Exploitation',
                'description' => 'Exploiting map boundaries, invulnerability glitches, or economy duplication bugs is prohibited. Report any discovered exploits immediately to staff via Discord tickets.',
                'order' => 3,
            ],
            [
                'category' => 'Chat & Communication',
                'title' => 'Keep Public Chat Clean',
                'description' => 'No spamming, advertising external servers or commercial services, or sharing malicious links. Respect community members across both the server and the website comments.',
                'order' => 4,
            ],
            [
                'category' => 'Clan & Competition',
                'title' => 'Competitive Integrity',
                'description' => 'Account sharing during competitive matches or rankings manipulation (boosting/winstreak trading) will lead to clan disqualification and ranking record wipe.',
                'order' => 5,
            ],
            [
                'category' => 'Enforcement & Appeals',
                'title' => 'Punishment & Ban Appeals',
                'description' => 'Staff decisions are final during active sessions. If you believe a punishment was unjustified, submit a formal appeal ticket on our official Discord server with unedited clip evidence.',
                'order' => 6,
            ],
        ];

        foreach ($defaultRules as $r) {
            Rule::firstOrCreate(
                ['title' => $r['title']],
                $r
            );
        }

        // Seed Welcome News Post
        NewsPost::firstOrCreate(
            ['slug' => 'welcome-to-hyde-pvp-bedrock'],
            [
                'user_id' => $admin->id,
                'title' => 'Welcome to the HYDE PVP Bedrock Community',
                'slug' => 'welcome-to-hyde-pvp-bedrock',
                'excerpt' => 'Welcome to the official home of HYDE PVP — the premier competitive Minecraft Bedrock PvP experience.',
                'body' => "Welcome to HYDE PVP!\n\nWe are excited to launch our official community website for Minecraft Bedrock PvP players. Here you can find our official server connection details, server rules, season rankings, and the latest server announcements.\n\n### What We Offer\n- **Fast, Responsive Bedrock Combat:** Custom knockback profiles and optimized hit-detection.\n- **Multiple Arenas & Duels:** Nodebuff, Combo, Boxing, BuildUHC, and Sumo.\n- **Active Competitive Community:** Join hundreds of players competing in weekly tournaments and clan battles.\n\n### Join Our Discord\nFor live tournament announcements, matchmaking, clan recruitment, and ticket support, head over to our official Discord server!\n\nSee you in the arena,\n— The HYDE PVP Team",
                'is_published' => true,
                'published_at' => now(),
            ]
        );
    }
}
