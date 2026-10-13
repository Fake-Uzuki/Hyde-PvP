<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $settings = SiteSetting::getAll();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'server_address' => ['required', 'string', 'max:100'],
            'server_port' => ['required', 'string', 'max:10'],
            'server_version' => ['required', 'string', 'max:50'],
            'discord_invite_url' => ['required', 'url', 'max:255'],
            'discord_server_id' => ['nullable', 'string', 'max:50'],
            'comment_word_limit' => ['required', 'integer', 'min:5', 'max:2000'],
            'comment_cooldown_seconds' => ['required', 'integer', 'min:0', 'max:600'],
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::set($key, (string) $value);
        }

        return redirect()->route('admin.settings.edit')->with('status', 'Site settings updated successfully.');
    }
}
