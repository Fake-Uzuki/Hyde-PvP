@extends('layouts.main')

@section('title', 'Server Info | HYDE PVP')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">

    <!-- Page Header -->
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-950/60 border border-cyan-500/30 text-cyan-300 text-xs font-semibold tracking-wide uppercase">
            Bedrock Dedicated Server
        </div>
        <h1 class="text-4xl sm:text-5xl font-black text-white font-gaming">
            SERVER <span class="gaming-gradient-text">INFORMATION</span>
        </h1>
        <p class="text-slate-300 text-base leading-relaxed">
            Everything you need to join and compete on HYDE PVP across Minecraft Bedrock Edition platforms.
        </p>
    </div>

    <!-- Quick Connection Card -->
    <div class="max-w-3xl mx-auto">
        <div class="gaming-card p-6 sm:p-8 space-y-6 border-purple-500/30">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <span class="text-xs uppercase tracking-wider text-purple-400 font-bold">Official Connection Details</span>
                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-950/60 text-emerald-400 border border-emerald-800/40 flex items-center gap-1.5 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Bedrock Edition
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Server Address -->
                <div class="p-4 rounded-xl bg-[#070A12] border border-slate-800 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold text-slate-500">Server IP / Address</span>
                        <!-- Live Player Counter Badge -->
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono {{ ($serverStatus['online'] ?? false) ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-500/40' : 'bg-purple-950/60 text-purple-300 border border-purple-700/40' }}"
                             title="Current live player count">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ ($serverStatus['online'] ?? false) ? 'bg-emerald-400 opacity-75' : 'bg-purple-400 opacity-50' }}"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 {{ ($serverStatus['online'] ?? false) ? 'bg-emerald-500' : 'bg-purple-500' }}"></span>
                            </span>
                            <span>{{ $serverStatus['players_online'] ?? 0 }} / {{ $serverStatus['players_max'] ?? 100 }} Online</span>
                        </div>
                    </div>
                    <div class="text-lg font-mono font-bold text-cyan-300 select-all">
                        {{ $settings['server_address'] ?? 'hydepvp.ultramc.co' }}
                    </div>
                </div>

                <!-- Server Port -->
                <div class="p-4 rounded-xl bg-[#070A12] border border-slate-800 space-y-1">
                    <span class="text-[11px] uppercase font-bold text-slate-500">Bedrock Port</span>
                    <div class="text-lg font-mono font-bold text-purple-300 select-all">
                        {{ $settings['server_port'] ?? '19015' }}
                    </div>
                </div>

                <!-- Supported Version -->
                <div class="p-4 rounded-xl bg-[#070A12] border border-slate-800 space-y-1">
                    <span class="text-[11px] uppercase font-bold text-slate-500">Supported Client Version</span>
                    <div class="text-base font-semibold text-slate-200">
                        {{ $settings['server_version'] ?? 'v1.21.x Bedrock' }}
                    </div>
                </div>

                <!-- Protocol -->
                <div class="p-4 rounded-xl bg-[#070A12] border border-slate-800 space-y-1">
                    <span class="text-[11px] uppercase font-bold text-slate-500">Game Edition</span>
                    <div class="text-base font-semibold text-slate-200">
                        Minecraft: Bedrock Edition
                    </div>
                </div>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <p class="text-xs text-slate-400">
                    Need help or connection troubleshooting? Ask in our Discord support channel.
                </p>
                <button type="button"
                        onclick="copyServerAddress(this, '{{ $settings['server_address'] ?? 'hydepvp.ultramc.co' }}')"
                        class="btn-primary text-xs py-3 px-6 w-full sm:w-auto">
                    Copy Server IP
                </button>
            </div>
        </div>
    </div>

    <!-- Step-by-Step Connection Guides -->
    <div class="space-y-8">
        <div class="text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white font-gaming">
                HOW TO <span class="gaming-gradient-text">CONNECT</span>
            </h2>
            <p class="text-slate-400 text-sm mt-1">Platform-specific instructions for Bedrock Edition.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Mobile Guide -->
            <div class="gaming-card p-6 space-y-4">
                <div class="w-10 h-10 rounded-xl bg-purple-900/30 border border-purple-500/30 flex items-center justify-center text-purple-400 font-bold">
                    1
                </div>
                <h3 class="text-lg font-bold text-white font-gaming">iOS & Android (Mobile)</h3>
                <ol class="text-xs text-slate-300 space-y-2 list-decimal list-inside leading-relaxed">
                    <li>Open Minecraft Bedrock and tap <strong>Play</strong>.</li>
                    <li>Navigate to the <strong>Servers</strong> tab.</li>
                    <li>Scroll down and tap <strong>Add Server</strong>.</li>
                    <li>Enter Server Name: <span class="text-purple-300 font-mono">HYDE PVP</span></li>
                    <li>Enter Address: <span class="text-cyan-300 font-mono">{{ $settings['server_address'] ?? 'hydepvp.ultramc.co' }}</span></li>
                    <li>Port: <span class="text-slate-200 font-mono">{{ $settings['server_port'] ?? '19015' }}</span></li>
                    <li>Tap <strong>Play</strong> or <strong>Save</strong> to join!</li>
                </ol>
            </div>

            <!-- Windows 10/11 Guide -->
            <div class="gaming-card p-6 space-y-4">
                <div class="w-10 h-10 rounded-xl bg-fuchsia-900/30 border border-fuchsia-500/30 flex items-center justify-center text-fuchsia-400 font-bold">
                    2
                </div>
                <h3 class="text-lg font-bold text-white font-gaming">Windows 10 / 11 (PC)</h3>
                <ol class="text-xs text-slate-300 space-y-2 list-decimal list-inside leading-relaxed">
                    <li>Launch Minecraft for Windows (Bedrock Edition).</li>
                    <li>Click <strong>Play</strong> → <strong>Servers</strong> tab.</li>
                    <li>Click <strong>Add Server</strong> at the bottom.</li>
                    <li>Server Name: <span class="text-purple-300 font-mono">HYDE PVP</span></li>
                    <li>Server Address: <span class="text-cyan-300 font-mono">{{ $settings['server_address'] ?? 'hydepvp.ultramc.co' }}</span></li>
                    <li>Port: <span class="text-slate-200 font-mono">{{ $settings['server_port'] ?? '19015' }}</span></li>
                    <li>Click <strong>Play</strong> and jump into the fight!</li>
                </ol>
            </div>

            <!-- Console Guide -->
            <div class="gaming-card p-6 space-y-4">
                <div class="w-10 h-10 rounded-xl bg-cyan-900/30 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-bold">
                    3
                </div>
                <h3 class="text-lg font-bold text-white font-gaming">Consoles (Xbox, PS, Switch)</h3>
                <div class="text-xs text-slate-300 space-y-2 leading-relaxed">
                    <p>Consoles require connecting via DNS redirect or BedrockConnect tools:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-400">
                        <li>Set your console Primary DNS to a public BedrockConnect DNS (e.g. 104.238.130.180).</li>
                        <li>Join any featured server on the list.</li>
                        <li>The BedrockConnect server list menu will appear.</li>
                        <li>Add <span class="text-cyan-300 font-mono">{{ $settings['server_address'] ?? 'hydepvp.ultramc.co' }}</span> and join.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional Discord Widget -->
    @if(!empty($settings['discord_server_id']))
        <div class="space-y-6 max-w-3xl mx-auto">
            <div class="text-center">
                <h2 class="text-xl font-bold text-white font-gaming">DISCORD COMMUNITY WIDGET</h2>
                <p class="text-xs text-slate-400 mt-1">Live voice & chat status</p>
            </div>
            <div class="gaming-card p-4 overflow-hidden flex justify-center">
                <iframe src="https://discord.com/api/guilds/{{ $settings['discord_server_id'] }}/widget.html?theme=dark"
                        width="350" height="400" allowtransparency="true" frameborder="0"
                        sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"
                        class="rounded-xl border border-slate-800"></iframe>
            </div>
        </div>
    @endif

</div>
@endsection
