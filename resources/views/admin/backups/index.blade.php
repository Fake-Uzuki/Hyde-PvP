@extends('admin.layout')

@section('title', 'Database & System Backups')

@section('content')
<div class="space-y-8" x-data="{ restoreModal: false, selectedBackup: '', uploadModal: false }">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-500/20 text-purple-300 border border-purple-500/30">
                    Super Admin Exclusive
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                    Engine: {{ strtoupper($dbConnection) }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white font-gaming tracking-wide flex items-center gap-2.5">
                <svg class="w-7 h-7 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>
                <span>Database & System Backups</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Download database snapshots, restore historical backups, and manage automated 6:00 AM PH schedules.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" @click="uploadModal = true"
                    class="btn-secondary text-xs py-2.5 px-4 inline-flex items-center gap-1.5 font-semibold">
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>Upload & Restore...</span>
            </button>

            <form method="POST" action="{{ route('admin.backups.store') }}">
                @csrf
                <button type="submit"
                        class="btn-primary text-xs py-2.5 px-5 inline-flex items-center gap-2 font-gaming tracking-wider uppercase shadow-lg shadow-purple-900/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Create Backup Now</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Automated World Clock & Philippine Schedule Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Philippine World Clock -->
        <div class="gaming-card p-5 space-y-2 border border-purple-500/20">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="uppercase tracking-wider font-bold">Philippine World Clock</span>
                <span class="px-2 py-0.5 rounded bg-purple-950/60 text-purple-300 font-mono text-[10px] border border-purple-800/40">
                    Asia/Manila (UTC+8)
                </span>
            </div>
            <div class="text-xl font-bold font-mono text-white">
                {{ $nowPh->format('h:i:s A') }}
            </div>
            <p class="text-[11px] text-slate-400">
                {{ $nowPh->format('l, F d, Y') }}
            </p>
        </div>

        <!-- Automated Schedule -->
        <div class="gaming-card p-5 space-y-2 border border-cyan-500/20">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="uppercase tracking-wider font-bold">Automated Schedule</span>
                <span class="px-2 py-0.5 rounded bg-cyan-950/60 text-cyan-300 font-mono text-[10px] border border-cyan-800/40">
                    Cron Active
                </span>
            </div>
            <div class="text-xl font-bold font-mono text-cyan-300">
                06:00 AM PHT
            </div>
            <p class="text-[11px] text-slate-400">
                Next run: <span class="text-slate-200 font-medium">{{ $nextBackupDiff }}</span> ({{ $next6AmPh->format('M d, 06:00 AM') }})
            </p>
        </div>

        <!-- Backup Retention Policy -->
        <div class="gaming-card p-5 space-y-2 border border-emerald-500/20">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="uppercase tracking-wider font-bold">Backup Archives</span>
                <span class="px-2 py-0.5 rounded bg-emerald-950/60 text-emerald-300 font-mono text-[10px] border border-emerald-800/40">
                    {{ count($backups) }} Available
                </span>
            </div>
            <div class="text-xl font-bold font-mono text-emerald-300">
                {{ count($backups) }} Snapshots
            </div>
            <p class="text-[11px] text-slate-400">
                Preserves up to 14 latest backups with automatic rotation.
            </p>
        </div>
    </div>

    <!-- Render Persistence Notice / Advisory -->
    <div class="p-4 sm:p-5 rounded-2xl bg-amber-950/30 border border-amber-500/30 space-y-2 text-xs">
        <div class="flex items-center gap-2 text-amber-300 font-bold uppercase tracking-wider">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Cloud Persistence Advisory for Render Web Service</span>
        </div>
        <p class="text-slate-300 leading-relaxed">
            Render Free Web Services use ephemeral container storage. While our daily 6:00 AM backups protect your data, local container files can reset if the container rebuilds without an external database.
            <strong>To make your data 100% permanent across all deployments:</strong> connect a free managed PostgreSQL database on Render (copy the Internal Database URL into your Render Environment as <code class="bg-black/50 px-1.5 py-0.5 rounded text-amber-300">DATABASE_URL</code>).
        </p>
    </div>

    <!-- Backups Table -->
    <div class="gaming-card overflow-hidden">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-base font-bold text-white font-gaming flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Generated System Backups ({{ count($backups) }})</span>
            </h2>
            <span class="text-xs text-slate-500 font-mono">storage/app/backups/</span>
        </div>

        @if(empty($backups))
            <div class="p-12 text-center text-slate-500 space-y-3">
                <svg class="w-12 h-12 mx-auto text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <div class="text-sm font-semibold text-slate-400">No backup archives found</div>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    Click "Create Backup Now" above to generate your first complete database snapshot.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-4 px-6 font-bold">Archive Filename</th>
                            <th class="py-4 px-6 font-bold">File Size</th>
                            <th class="py-4 px-6 font-bold">Created (Philippine Time)</th>
                            <th class="py-4 px-6 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($backups as $b)
                            <tr class="hover:bg-slate-900/50 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                        <span class="font-mono text-xs font-bold text-white">{{ $b['filename'] }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-mono text-xs text-slate-300">
                                    {{ $b['size_formatted'] }}
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-400">
                                    <div class="font-medium text-slate-200">{{ $b['created_at_ph'] }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $b['created_at_diff'] }}</div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Download -->
                                        <a href="{{ route('admin.backups.download', $b['filename']) }}"
                                           class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-purple-300 hover:text-white border border-slate-700 transition"
                                           title="Download Backup ZIP">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>

                                        <!-- Restore Button -->
                                        <button type="button"
                                                @click="selectedBackup = '{{ $b['filename'] }}'; restoreModal = true"
                                                class="p-2 rounded-xl bg-slate-800 hover:bg-amber-950/60 text-amber-300 border border-slate-700 hover:border-amber-500/40 transition"
                                                title="Restore this backup">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>

                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('admin.backups.destroy', $b['filename']) }}"
                                              onsubmit="return confirm('Are you sure you want to delete backup {{ $b['filename'] }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 rounded-xl bg-slate-800 hover:bg-rose-950/60 text-rose-400 hover:text-rose-300 border border-slate-700 hover:border-rose-500/40 transition"
                                                    title="Delete Backup">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Modal 1: Restore Existing Snapshot Confirmation -->
    <div x-show="restoreModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        <div @click.away="restoreModal = false"
             class="gaming-card max-w-md w-full p-6 space-y-4 border border-amber-500/40">
            <div class="flex items-center gap-2 text-amber-400 font-bold font-gaming">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Confirm Database Restoration</span>
            </div>
            <p class="text-xs text-slate-300 leading-relaxed">
                You are about to restore the database from snapshot:
                <br>
                <code class="text-amber-300 font-mono font-bold block mt-1" x-text="selectedBackup"></code>
                <br>
                Existing tables will be overwritten with the data from this backup.
            </p>

            <form method="POST" action="{{ route('admin.backups.restore') }}" class="space-y-4 pt-2">
                @csrf
                <input type="hidden" name="filename" :value="selectedBackup" />

                <div class="flex justify-end gap-2.5">
                    <button type="button" @click="restoreModal = false" class="btn-secondary text-xs py-2 px-4">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs uppercase tracking-wide transition">
                        Confirm & Restore
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Upload & Restore from Computer -->
    <div x-show="uploadModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        <div @click.away="uploadModal = false"
             class="gaming-card max-w-lg w-full p-6 space-y-5 border border-purple-500/40">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white font-gaming flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Upload & Restore Backup Archive</span>
                </h3>
                <button type="button" @click="uploadModal = false" class="text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                Select a previously downloaded <code class="text-purple-300">.zip</code> or <code class="text-purple-300">.sqlite</code> backup archive from your local computer to restore to the server.
            </p>

            <form method="POST" action="{{ route('admin.backups.restore') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Select Backup File (.zip or .sqlite)</label>
                    <input type="file" name="backup_file" required accept=".zip,.sqlite"
                           class="block w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-600/20 file:text-purple-300 hover:file:bg-purple-600/30 file:cursor-pointer border border-slate-800 rounded-xl p-2 bg-[#070A12]" />
                </div>

                <div class="flex justify-end gap-2.5 pt-2">
                    <button type="button" @click="uploadModal = false" class="btn-secondary text-xs py-2 px-4">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary text-xs py-2 px-5 font-gaming uppercase tracking-wider">
                        Upload & Restore
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
