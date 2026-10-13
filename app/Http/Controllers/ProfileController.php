<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information and Minecraft skin.
     */
    public function update(ProfileUpdateRequest $request, \App\Services\MinecraftSkinService $skinService): RedirectResponse
    {
        $user = $request->user();

        // 1. Handle skin upload if present
        if ($request->hasFile('skin')) {
            $skinError = $skinService->validateSkin($request->file('skin'));
            if ($skinError) {
                return back()->withErrors(['skin' => $skinError])->withInput();
            }

            // Remove previous custom skin files if any
            $skinService->deleteOldSkin($user->skin_path, $user->avatar_head_path);

            // Store new skin and extract head
            $stored = $skinService->saveSkinAndExtractHead($request->file('skin'), $user->id);
            $user->skin_path = $stored['skin_path'];
            $user->avatar_head_path = $stored['avatar_head_path'];

            if ($user->minecraft_gamertag) {
                \App\Services\PlayerAvatarService::clearCache($user->minecraft_gamertag);
            }
        } elseif ($request->boolean('remove_skin')) {
            // User requested to remove custom skin and revert to Bedrock skin / default
            $skinService->deleteOldSkin($user->skin_path, $user->avatar_head_path);
            $user->skin_path = null;
            $user->avatar_head_path = null;

            if ($user->minecraft_gamertag) {
                \App\Services\PlayerAvatarService::clearCache($user->minecraft_gamertag);
            }
        }

        // 2. Handle basic profile fields
        $oldGamertag = $user->minecraft_gamertag;
        $user->fill($request->safe()->only(['name', 'email', 'minecraft_gamertag']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($oldGamertag && $oldGamertag !== $user->minecraft_gamertag) {
            \App\Services\PlayerAvatarService::clearCache($oldGamertag);
        }
        if ($user->minecraft_gamertag) {
            \App\Services\PlayerAvatarService::clearCache($user->minecraft_gamertag);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
