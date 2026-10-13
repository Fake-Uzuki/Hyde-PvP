<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('player:verify {code} {gamertag=Uzuki} {--xuid=}', function (string $code, string $gamertag) {
    $xuid = $this->option('xuid') ?: '25354' . rand(10000000, 99999999);
    
    $record = \App\Models\PlayerVerificationCode::where('code', trim($code))
        ->where('is_used', false)
        ->where('expires_at', '>', now())
        ->first();

    if (! $record) {
        $this->error("❌ Invalid or expired code: '{$code}'. Generate a fresh code on http://localhost:8000/profile!");
        return 1;
    }

    $user = $record->user;
    if (! $user) {
        $this->error("❌ User not found for code '{$code}'.");
        return 1;
    }

    $oldGamertag = $user->minecraft_gamertag;
    $user->minecraft_gamertag = trim($gamertag);
    $user->xuid = $xuid;
    $user->is_verified = true;
    $user->verified_at = now();
    $user->save();

    $record->update(['is_used' => true]);

    if ($oldGamertag) {
        \App\Services\PlayerAvatarService::clearCache($oldGamertag);
    }
    \App\Services\PlayerAvatarService::clearCache($gamertag);

    $this->info("========================================");
    $this->info("✔ SUCCESS! Simulated in-game verification:");
    $this->line("👤 Website User    : {$user->name} ({$user->email})");
    $this->line("🎮 Linked Gamertag : {$gamertag}");
    $this->line("🆔 XUID            : {$xuid}");
    $this->info("========================================");
    $this->comment("Check http://localhost:8000/profile — the profile will update instantly to Verified!");
    return 0;
})->purpose('Simulate an in-game /verify <code> command from the console for local testing');

