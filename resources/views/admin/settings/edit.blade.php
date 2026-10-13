@extends('admin.layout', ['title' => 'Site Settings'])

@section('admin_content')
<div class="max-w-4xl space-y-6">

    <div>
        <h2 class="text-xl font-bold text-white font-gaming">Site & Community Settings</h2>
        <p class="text-xs text-slate-400">Configure Minecraft Bedrock server parameters, Discord invite links, and comment rate limits.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="gaming-card p-6 sm:p-8 space-y-8">
        @csrf
        @method('PUT')

        <!-- Bedrock Server Information -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-purple-400 border-b border-slate-800 pb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                </svg>
                Minecraft Bedrock Server Settings
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="server_address" class="block text-xs uppercase font-bold text-slate-300 mb-1">Server Host / Address</label>
                    <input type="text" name="server_address" id="server_address" value="{{ old('server_address', $settings['server_address'] ?? 'hydepvp.ultramc.co') }}" required
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-cyan-300 font-mono focus:outline-none focus:border-purple-500" />
                    <p class="text-[11px] text-slate-500 mt-1">Displayed on the homepage and server info quick connect card.</p>
                </div>

                <div>
                    <label for="server_port" class="block text-xs uppercase font-bold text-slate-300 mb-1">Bedrock Port</label>
                    <input type="text" name="server_port" id="server_port" value="{{ old('server_port', $settings['server_port'] ?? '19015') }}" required
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-purple-300 font-mono focus:outline-none focus:border-purple-500" />
                    <p class="text-[11px] text-slate-500 mt-1">Default Bedrock port is 19015.</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="server_version" class="block text-xs uppercase font-bold text-slate-300 mb-1">Supported Client Version</label>
                    <input type="text" name="server_version" id="server_version" value="{{ old('server_version', $settings['server_version'] ?? 'v1.21.x (Bedrock)') }}" required
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-purple-500" />
                </div>
            </div>
        </div>

        <!-- Discord Community Settings -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-indigo-400 border-b border-slate-800 pb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
                Discord Integration Settings
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="discord_invite_url" class="block text-xs uppercase font-bold text-slate-300 mb-1">Discord Invite URL</label>
                    <input type="url" name="discord_invite_url" id="discord_invite_url" value="{{ old('discord_invite_url', $settings['discord_invite_url'] ?? 'https://discord.gg/hydepvp') }}" required
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-indigo-300 font-mono focus:outline-none focus:border-purple-500" />
                    <p class="text-[11px] text-slate-500 mt-1">Configurable invite link used across the website buttons and event pointers.</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="discord_server_id" class="block text-xs uppercase font-bold text-slate-300 mb-1">Discord Server ID (Optional Widget)</label>
                    <input type="text" name="discord_server_id" id="discord_server_id" value="{{ old('discord_server_id', $settings['discord_server_id'] ?? '') }}" placeholder="e.g. 112233445566778899"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 font-mono focus:outline-none focus:border-purple-500" />
                    <p class="text-[11px] text-slate-500 mt-1">If set, embeds the official Discord widget on the Server Info page. Enable Widget in Discord Server Settings → Widget.</p>
                </div>
            </div>
        </div>

        <!-- Comment Anti-Spam & Limits -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-fuchsia-400 border-b border-slate-800 pb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Comment System & Anti-Spam Rules
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="comment_word_limit" class="block text-xs uppercase font-bold text-slate-300 mb-1">Max Word Limit Per Comment</label>
                    <input type="number" name="comment_word_limit" id="comment_word_limit" value="{{ old('comment_word_limit', $settings['comment_word_limit'] ?? 500) }}" required min="5" max="2000"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500" />
                    <p class="text-[11px] text-slate-500 mt-1">Default is 500 words. Enforced on both frontend and backend.</p>
                </div>

                <div>
                    <label for="comment_cooldown_seconds" class="block text-xs uppercase font-bold text-slate-300 mb-1">Cooldown Duration (Seconds)</label>
                    <input type="number" name="comment_cooldown_seconds" id="comment_cooldown_seconds" value="{{ old('comment_cooldown_seconds', $settings['comment_cooldown_seconds'] ?? 60) }}" required min="0" max="600"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500" />
                    <p class="text-[11px] text-slate-500 mt-1">Default is 60 seconds per user between comments.</p>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end">
            <button type="submit" class="btn-primary text-xs py-2.5 px-6">
                Save Site Settings
            </button>
        </div>
    </form>

</div>
@endsection
