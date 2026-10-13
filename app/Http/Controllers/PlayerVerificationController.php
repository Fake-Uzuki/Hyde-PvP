<?php

namespace App\Http\Controllers;

use App\Models\PlayerVerificationCode;
use App\Services\PlayerAvatarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerVerificationController extends Controller
{
    /**
     * Generate or fetch the user's active in-game verification code.
     */
    public function generate(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        // Check if there is already an active, valid code
        $activeCode = PlayerVerificationCode::where('user_id', $user->id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $activeCode) {
            $activeCode = PlayerVerificationCode::generateForUser($user, 15);
        }

        $remainingSeconds = (int) max(0, now()->diffInSeconds($activeCode->expires_at, false));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'code' => $activeCode->code,
                'command' => "/verify {$activeCode->code}",
                'expires_in_seconds' => $remainingSeconds,
            ]);
        }

        return back()->with('verification_code', $activeCode->code)
            ->with('verification_command', "/verify {$activeCode->code}");
    }

    /**
     * Check if the logged-in user is verified (polled by UI).
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'is_verified' => (bool) $user->is_verified,
            'gamertag' => $user->minecraft_gamertag,
            'xuid' => $user->xuid,
            'verified_at' => $user->verified_at?->format('M d, Y \a\t g:i A'),
        ]);
    }

    /**
     * In-Game API endpoint called by the Bedrock Server when a player types `/verify <code>`.
     * Protected by the server's private secret key.
     */
    public function verifyFromGame(Request $request): JsonResponse
    {
        // 1. Authenticate server request
        $expectedKey = config('services.hyde.server_key', env('HYDE_SERVER_KEY', 'hyde-pvp-secret-key-19015'));
        $providedKey = $request->header('X-Server-Key') ?? $request->input('server_key');

        if (! $providedKey || ! hash_equals($expectedKey, (string) $providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid server key.',
            ], 401);
        }

        // 2. Validate input
        $validated = $request->validate([
            'code' => ['required', 'string'],
            'gamertag' => ['required', 'string', 'max:32'],
            'xuid' => ['nullable', 'string', 'max:32'],
        ]);

        $code = trim($validated['code']);
        $gamertag = trim($validated['gamertag']);
        $xuid = !empty($validated['xuid']) ? trim($validated['xuid']) : null;

        // 3. Find active verification code
        $record = PlayerVerificationCode::where('code', $code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code. Generate a new code on the website.',
            ], 404);
        }

        $user = $record->user;
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Associated user account not found.',
            ], 404);
        }

        // 4. Bind player identity to user
        $oldGamertag = $user->minecraft_gamertag;
        $user->minecraft_gamertag = $gamertag;
        $user->xuid = $xuid;
        $user->is_verified = true;
        $user->verified_at = now();
        $user->save();

        // 5. Invalidate code
        $record->update(['is_used' => true]);

        // 6. Invalidate avatar & profile cache
        if ($oldGamertag) {
            PlayerAvatarService::clearCache($oldGamertag);
        }
        PlayerAvatarService::clearCache($gamertag);

        return response()->json([
            'success' => true,
            'message' => "Successfully linked {$gamertag} to website account '{$user->name}'!",
            'user' => $user->name,
            'gamertag' => $gamertag,
            'xuid' => $xuid,
        ]);
    }

    /**
     * Unlink verified gamertag.
     */
    public function unlink(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->minecraft_gamertag) {
            PlayerAvatarService::clearCache($user->minecraft_gamertag);
        }

        $user->is_verified = false;
        $user->xuid = null;
        $user->verified_at = null;
        $user->save();

        return back()->with('status', 'Account unlinked successfully.');
    }
}
