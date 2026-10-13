@extends('layouts.main')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">

    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-950/60 border border-purple-500/30 text-purple-300 text-xs font-semibold tracking-wide uppercase">
            Community Guidelines
        </div>
        <h1 class="text-4xl sm:text-5xl font-black text-white font-gaming">
            OFFICIAL <span class="gaming-gradient-text">SERVER RULES</span>
        </h1>
        <p class="text-slate-300 text-base leading-relaxed">
            By connecting to HYDE PVP, you agree to adhere strictly to all server rules. Ignorance of rules is not an excuse.
        </p>
    </div>

    @if($rules->isEmpty())
        <div class="gaming-card p-12 text-center text-slate-400">
            <h2 class="text-lg font-bold text-white mb-2">No Rules Published Yet</h2>
            <p class="text-xs text-slate-400">Server rules are currently being updated by administrators.</p>
        </div>
    @else
        <div class="space-y-10">
            @foreach($rules as $category => $categoryRules)
                <div class="space-y-4">
                    <div class="flex items-center gap-3 border-b border-slate-800 pb-3">
                        <span class="w-3 h-3 rounded-full bg-gradient-to-r from-purple-500 to-fuchsia-500"></span>
                        <h2 class="text-xl font-bold text-white font-gaming tracking-wide uppercase">
                            {{ $category }}
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        @foreach($categoryRules as $rule)
                            <div class="gaming-card p-5 space-y-2">
                                <div class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-lg bg-purple-950/60 border border-purple-800/40 text-purple-300 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">
                                        §
                                    </span>
                                    <div>
                                        <h3 class="text-base font-bold text-white font-gaming">
                                            {{ $rule->title }}
                                        </h3>
                                        <p class="text-slate-400 text-sm leading-relaxed mt-1">
                                            {{ $rule->description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Ban Appeal Notice -->
    <div class="gaming-card p-6 sm:p-8 bg-gradient-to-r from-slate-900 via-slate-900/90 to-purple-950/40 border-purple-500/20 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="space-y-2 text-center sm:text-left">
            <h3 class="text-lg font-bold text-white font-gaming">Punishment & Ban Appeals</h3>
            <p class="text-xs text-slate-400 max-w-lg leading-relaxed">
                If you believe a punishment or kick was issued in error, create a support ticket on our official Discord. Have your gamertag and timestamp ready.
            </p>
        </div>
        <a href="{{ $discordUrl }}" target="_blank" rel="noopener noreferrer" class="btn-discord text-xs py-3 px-6 shrink-0">
            Submit Discord Appeal
        </a>
    </div>

</div>
@endsection
