<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UhcWinner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UhcWinnersController extends Controller
{
    /**
     * Display a listing of UHC tournament winners.
     */
    public function index(): View
    {
        $tournaments = UhcWinner::orderBy('event_date', 'desc')->get();

        return view('admin.uhc.index', compact('tournaments'));
    }

    /**
     * Show form for posting a new UHC tournament winner.
     */
    public function create(): View
    {
        return view('admin.uhc.create');
    }

    /**
     * Store a newly created UHC tournament winner.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'winner_gamertag' => ['required', 'string', 'max:50'],
            'runner_up_gamertag' => ['nullable', 'string', 'max:50'],
            'winner_kills' => ['required', 'integer', 'min:0'],
            'event_date' => ['required', 'date'],
            'game_mode' => ['required', 'string', 'max:50'],
            'prize' => ['nullable', 'string', 'max:150'],
            'recap_url' => ['nullable', 'url', 'max:255'],
        ]);

        UhcWinner::create($validated);

        return redirect()->route('admin.uhc.index')->with('status', 'UHC Tournament Winner successfully published.');
    }

    /**
     * Show form for editing an existing UHC tournament record.
     */
    public function edit(UhcWinner $uhc): View
    {
        return view('admin.uhc.edit', compact('uhc'));
    }

    /**
     * Update an existing UHC tournament record.
     */
    public function update(Request $request, UhcWinner $uhc): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'winner_gamertag' => ['required', 'string', 'max:50'],
            'runner_up_gamertag' => ['nullable', 'string', 'max:50'],
            'winner_kills' => ['required', 'integer', 'min:0'],
            'event_date' => ['required', 'date'],
            'game_mode' => ['required', 'string', 'max:50'],
            'prize' => ['nullable', 'string', 'max:150'],
            'recap_url' => ['nullable', 'url', 'max:255'],
        ]);

        $uhc->update($validated);

        return redirect()->route('admin.uhc.index')->with('status', 'UHC Tournament record updated.');
    }

    /**
     * Remove a UHC tournament record.
     */
    public function destroy(UhcWinner $uhc): RedirectResponse
    {
        $uhc->delete();

        return redirect()->route('admin.uhc.index')->with('status', 'UHC Tournament record removed.');
    }
}
