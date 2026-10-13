<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\SiteSetting;
use Illuminate\View\View;

class RulesController extends Controller
{
    /**
     * Display the official server rules.
     */
    public function index(): View
    {
        $rules = Rule::orderBy('order', 'asc')
            ->get()
            ->groupBy('category');

        $discordUrl = SiteSetting::get('discord_invite_url', 'https://discord.gg/hydepvp');

        return view('rules', compact('rules', 'discordUrl'));
    }
}
