<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RulesController extends Controller
{
    public function index(): View
    {
        $rules = Rule::orderBy('category')->orderBy('order')->get();

        return view('admin.rules.index', compact('rules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        Rule::create([
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()->route('admin.rules.index')->with('status', 'Rule added successfully.');
    }

    public function update(Request $request, Rule $rule): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $rule->update([
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()->route('admin.rules.index')->with('status', 'Rule updated successfully.');
    }

    public function destroy(Rule $rule): RedirectResponse
    {
        $rule->delete();

        return redirect()->route('admin.rules.index')->with('status', 'Rule deleted successfully.');
    }
}
