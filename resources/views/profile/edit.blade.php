@extends('layouts.main')

@section('title', 'Account Settings | HYDE PVP')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

    <!-- Header & Hero Profile Badge -->
    <div class="gaming-card p-6 sm:p-8 relative overflow-hidden border border-purple-500/20">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-5">
                <!-- User Head Avatar -->
                <div class="relative group">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-slate-900 border-2 border-purple-500/50 p-1 overflow-hidden shadow-xl shadow-purple-950/40 shrink-0">
                        <img src="{{ $user->avatar_url }}"
                             alt="{{ $user->name }}"
                             class="w-full h-full object-cover pixelated rounded-xl" />
                    </div>
                    @if($user->hasCustomSkin())
                        <span class="absolute -bottom-1 -right-1 px-1.5 py-0.5 rounded-md bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-wider" title="Custom Skin Active">
                            Skin ✓
                        </span>
                    @endif
                </div>

                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-2xl sm:text-3xl font-black text-white font-gaming">
                            {{ $user->name }}
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider font-mono
                            {{ $user->isAdmin() ? 'bg-purple-950 text-purple-300 border border-purple-500/50' : ($user->isModerator() ? 'bg-cyan-950 text-cyan-300 border border-cyan-500/50' : 'bg-slate-800 text-slate-300 border border-slate-700') }}">
                            {{ $user->role }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-400 font-mono">
                        {{ $user->email }}
                    </p>

                    @if($user->minecraft_gamertag)
                        <div class="flex items-center gap-2 pt-1 text-xs">
                            <span class="text-slate-400">Bedrock Gamertag:</span>
                            <a href="{{ route('players.show', $user->minecraft_gamertag) }}"
                               class="font-mono font-bold text-cyan-300 hover:text-cyan-200 hover:underline flex items-center gap-1">
                                <span>{{ $user->minecraft_gamertag }}</span>
                                <span class="text-[11px]">↗</span>
                            </a>
                        </div>
                    @else
                        <p class="text-[11px] text-amber-400 pt-1">
                            ⚠️ No Minecraft Bedrock gamertag linked yet. Add one below to link to leaderboard rankings.
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                @if($user->minecraft_gamertag)
                    <a href="{{ route('players.show', $user->minecraft_gamertag) }}" class="btn-secondary text-xs py-2.5 px-4 w-full md:w-auto text-center">
                        View Combat Profile
                    </a>
                @endif
                @if($user->isStaff())
                    <a href="{{ route('admin.dashboard') }}" class="btn-primary text-xs py-2.5 px-4 w-full md:w-auto text-center">
                        Admin Dashboard
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Alerts -->
    @if(session('status') === 'profile-updated')
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-sm flex items-center justify-between gap-3 shadow-lg">
            <div class="flex items-center gap-2">
                <span>✓</span>
                <span>Your profile information and skin preferences have been successfully updated.</span>
            </div>
            <span class="text-xs text-emerald-400 font-mono">Saved</span>
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-sm flex items-center gap-2 shadow-lg">
            <span>✓</span>
            <span>Your password has been changed securely.</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left Column: Skin Uploader & Profile Info (7 cols) -->
        <div class="lg:col-span-7 space-y-8">

            <!-- Card 1: Minecraft Skin & Head Icon -->
            <div class="gaming-card p-6 sm:p-8 space-y-6" x-data="skinUploader()">
                <div class="flex items-start justify-between border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-white font-gaming flex items-center gap-2">
                            <span>🎮</span> Minecraft Skin & Avatar Icon
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Upload your official Minecraft skin file (.png) to display your head icon across rankings and account menus.
                        </p>
                    </div>
                    @if($user->hasCustomSkin())
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 uppercase">
                            Custom Skin Active
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Hidden fields to preserve other values if submitting here -->
                    <input type="hidden" name="name" value="{{ $user->name }}" />
                    <input type="hidden" name="email" value="{{ $user->email }}" />
                    <input type="hidden" name="minecraft_gamertag" value="{{ $user->minecraft_gamertag }}" />

                    <!-- Current Skin vs Preview -->
                    <div class="p-4 rounded-xl bg-[#070A12] border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-5">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-slate-900 border border-purple-500/40 overflow-hidden flex items-center justify-center shrink-0 shadow-md">
                                <!-- Canvas for live uploaded skin head preview -->
                                <canvas x-ref="headCanvas" width="64" height="64" class="w-full h-full object-cover pixelated" x-show="hasClientPreview"></canvas>
                                <!-- Fallback to server avatar if no client preview yet -->
                                <img src="{{ $user->avatar_url }}" alt="Current Avatar" class="w-full h-full object-cover pixelated" x-show="!hasClientPreview" />
                            </div>
                            <div>
                                <span class="text-[11px] uppercase font-bold text-purple-300 block">Account Icon Preview</span>
                                <span class="text-xs text-slate-300" x-text="previewLabel">
                                    {{ $user->hasCustomSkin() ? 'Custom Head Active' : 'Bedrock / Default Icon' }}
                                </span>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Head + 3D outer hat layer composited automatically
                                </p>
                            </div>
                        </div>

                        @if($user->hasCustomSkin())
                            <div class="flex items-center gap-2 self-end sm:self-center">
                                @if($user->skin_url)
                                    <a href="{{ $user->skin_url }}" download="skin.png" class="text-xs text-cyan-400 hover:text-cyan-300 underline font-mono">
                                        Download Skin PNG
                                    </a>
                                @endif
                                <button type="submit" name="remove_skin" value="1"
                                        onclick="return confirm('Remove custom skin and revert to Bedrock skin?')"
                                        class="text-xs text-rose-400 hover:text-rose-300 px-3 py-1.5 rounded-lg bg-rose-950/30 border border-rose-800/40 transition">
                                    Remove Skin
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Client-side validation message banner -->
                    <div x-show="errorMessage" x-text="errorMessage" class="p-3 rounded-lg bg-rose-950/80 border border-rose-500/50 text-xs text-rose-200" style="display: none;"></div>

                    <!-- Skin File Input -->
                    <div>
                        <label for="skin" class="block text-xs uppercase font-bold text-slate-300 mb-1.5">
                            Upload Minecraft Skin PNG
                        </label>
                        <input type="file"
                               id="skin"
                               name="skin"
                               accept="image/png"
                               @change="handleFileSelect($event)"
                               class="block w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-600/20 file:text-purple-300 hover:file:bg-purple-600/30 file:cursor-pointer border border-slate-800 rounded-xl p-2 bg-[#070A12] focus:outline-none focus:border-purple-500 transition" />
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1.5">
                            <span>Format: <strong>64x64</strong> or <strong>64x32</strong> PNG</span>
                            <span>Max size: <strong>2MB</strong></span>
                        </div>
                        @error('skin')
                            <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" :disabled="isInvalid" class="btn-primary text-xs py-2.5 px-6 disabled:opacity-50 disabled:cursor-not-allowed">
                            Upload & Save Skin
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Profile Information -->
            <div class="gaming-card p-6 sm:p-8 space-y-6">
                <div class="border-b border-slate-800 pb-4">
                    <h2 class="text-lg font-bold text-white font-gaming flex items-center gap-2">
                        <span>👤</span> Profile Information
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Update your account details and linked Bedrock gamertag.
                    </p>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-xs uppercase font-bold text-slate-300 mb-1">Display Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500 font-medium" />
                        @error('name')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs uppercase font-bold text-slate-300 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500 font-mono" />
                        @error('email')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2 text-xs text-amber-300 flex items-center justify-between p-2.5 rounded-lg bg-amber-950/30 border border-amber-800/40">
                                <span>Your email address is unverified.</span>
                                <button form="send-verification" class="underline text-amber-200 hover:text-white font-medium">
                                    Resend Verification Email
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Minecraft Gamertag -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="minecraft_gamertag" class="block text-xs uppercase font-bold text-slate-300">
                                Minecraft Bedrock Gamertag
                            </label>
                            <span class="text-[11px] text-purple-400 font-mono">Linked to Leaderboards</span>
                        </div>
                        <input type="text"
                               id="minecraft_gamertag"
                               name="minecraft_gamertag"
                               value="{{ old('minecraft_gamertag', $user->minecraft_gamertag) }}"
                               placeholder="e.g. ShadowPvP_99"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-purple-500 font-mono" />
                        <p class="text-[11px] text-slate-500 mt-1">
                            Used to correlate your website account with your in-game Bedrock PvP combat statistics and leaderboard rank.
                        </p>
                        @error('minecraft_gamertag')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role (Read-only) -->
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-400 mb-1">Account Role</label>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 font-bold text-xs uppercase font-mono">
                                {{ $user->role }}
                            </span>
                            <span class="text-xs text-slate-500">Privileges managed by HYDE PVP server administrators</span>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn-primary text-xs py-2.5 px-6">
                            Save Profile Changes
                        </button>
                    </div>
                </form>

                <!-- Hidden verification form -->
                <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </div>

        <!-- Right Column: Verification, Password & Account Deletion (5 cols) -->
        <div class="lg:col-span-5 space-y-8">

            <!-- Card: In-Game Bedrock Verification (The Gold Standard) -->
            <div class="gaming-card p-6 sm:p-8 space-y-5 border border-cyan-500/20" x-data="inGameVerifier()">
                <div class="flex items-start justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-white font-gaming flex items-center gap-2">
                            <span>🛡️</span> Bedrock Verification
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Verify in-game ownership on <span class="font-mono text-cyan-300">hydepvp.ultramc.co</span>
                        </p>
                    </div>

                    @if($user->is_verified)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 uppercase">
                            Verified ✓
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 uppercase">
                            Unverified
                        </span>
                    @endif
                </div>

                @if($user->is_verified)
                    <!-- Verified State -->
                    <div class="space-y-4">
                        <div class="p-4 rounded-xl bg-emerald-950/30 border border-emerald-500/30 space-y-2">
                            <div class="flex items-center gap-2 text-emerald-300 font-bold text-xs uppercase tracking-wide">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Verified Bedrock Identity
                            </div>
                            <div class="text-sm font-mono text-white font-bold">
                                {{ $user->minecraft_gamertag }}
                            </div>
                            <p class="text-[11px] text-slate-400">
                                Verified {{ $user->verified_at ? $user->verified_at->format('M d, Y') : 'online' }}. Your rank, combat profile, and custom skins are officially protected.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('profile.verify.unlink') }}" onsubmit="return confirm('Are you sure you want to unlink your verified Minecraft gamertag?')">
                            @csrf
                            <button type="submit" class="text-xs text-slate-400 hover:text-rose-400 underline">
                                Unlink Bedrock Gamertag
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Unverified State with In-Game Verification Generator -->
                    <div class="space-y-4">
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Prove you own this gamertag to unlock the <strong class="text-cyan-300">Verified Player Badge</strong> on your profile and leaderboards.
                        </p>

                        <div x-show="!code" class="pt-1">
                            <button type="button"
                                    @click="requestCode()"
                                    :disabled="loading"
                                    class="w-full btn-primary text-xs py-2.5 px-4 flex items-center justify-center gap-2 disabled:opacity-50">
                                <span x-show="!loading">Generate In-Game Code</span>
                                <span x-show="loading" style="display:none;">Contacting Server...</span>
                            </button>
                        </div>

                        <!-- Generated Code & In-Game Command Display -->
                        <div x-show="code" x-cloak class="space-y-3" style="display: none;">
                            <div class="p-4 rounded-xl bg-purple-950/40 border border-purple-500/40 text-center space-y-2">
                                <span class="text-[10px] uppercase tracking-wider text-purple-300 font-bold block">
                                    Your 6-Digit In-Game Code
                                </span>
                                <div class="text-2xl font-black font-mono tracking-widest text-white" x-text="code"></div>
                                <p class="text-[11px] text-slate-400">
                                    Expires in <span class="text-amber-400 font-mono font-bold" x-text="countdownText">15:00</span>
                                </p>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-1.5">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">
                                    Step 2: Run This In-Game
                                </span>
                                <div class="flex items-center justify-between gap-2 p-2 rounded-lg bg-[#070A12] border border-slate-800">
                                    <code class="text-xs font-mono font-bold text-cyan-300 select-all" x-text="command"></code>
                                    <button type="button" @click="copyCommand()" class="text-[11px] text-purple-400 hover:text-purple-300 font-semibold px-2 py-0.5 rounded bg-purple-950 border border-purple-800/40">
                                        <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-500">
                                    Log in to <strong class="text-slate-400">hydepvp.ultramc.co:19015</strong> and type the command in chat.
                                </p>
                            </div>

                            <div class="flex items-center gap-2 text-[11px] text-slate-400 justify-center pt-1">
                                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                                <span>Listening for in-game verification...</span>
                            </div>
                        </div>

                        <!-- Success Celebratory Banner -->
                        <div x-show="justVerified" x-cloak class="p-3 rounded-xl bg-emerald-950 border border-emerald-500 text-xs text-emerald-300 text-center font-bold" style="display: none;">
                            🎉 Verified successfully in-game! Reloading...
                        </div>
                    </div>
                @endif
            </div>

            <!-- Card 3: Update Password -->
            <div class="gaming-card p-6 sm:p-8 space-y-6">
                <div class="border-b border-slate-800 pb-4">
                    <h2 class="text-lg font-bold text-white font-gaming flex items-center gap-2">
                        <span>🔒</span> Update Password
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Ensure your account uses a secure password to prevent unauthorized access.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs uppercase font-bold text-slate-300 mb-1">Current Password</label>
                        <input type="password" id="current_password" name="current_password" autocomplete="current-password" required
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500" />
                        @error('current_password', 'updatePassword')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs uppercase font-bold text-slate-300 mb-1">New Password</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" required
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500" />
                        @error('password', 'updatePassword')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs uppercase font-bold text-slate-300 mb-1">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500" />
                        @error('password_confirmation', 'updatePassword')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn-primary text-xs py-2.5 px-6">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 4: Danger Zone: Delete Account -->
            <div class="gaming-card p-6 sm:p-8 space-y-4 border border-rose-900/30" x-data="{ confirmingDeletion: false }">
                <div class="border-b border-slate-800 pb-3">
                    <h2 class="text-base font-bold text-rose-400 font-gaming flex items-center gap-2">
                        <span>⚠️</span> Danger Zone
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Permanently delete your HYDE PVP account and associated comment data.
                    </p>
                </div>

                <div x-show="!confirmingDeletion" class="pt-2">
                    <button type="button" @click="confirmingDeletion = true"
                            class="px-4 py-2 rounded-xl bg-rose-950/40 hover:bg-rose-900/60 text-rose-300 border border-rose-800/40 text-xs font-bold transition">
                        Delete Account...
                    </button>
                </div>

                <!-- Inline Confirmation Form -->
                <div x-show="confirmingDeletion" x-cloak class="p-4 rounded-xl bg-rose-950/30 border border-rose-800/50 space-y-4">
                    <p class="text-xs text-rose-200">
                        Are you sure you want to delete your account? Enter your password to permanently confirm:
                    </p>

                    <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-3">
                        @csrf
                        @method('DELETE')

                        <input type="password" name="password" required placeholder="Enter password to confirm"
                               class="w-full bg-[#070A12] border border-rose-800/50 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500" />
                        @error('password', 'userDeletion')
                            <p class="text-rose-400 text-xs">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center justify-end gap-2 pt-1">
                            <button type="button" @click="confirmingDeletion = false" class="btn-secondary text-xs py-1.5 px-3">
                                Cancel
                            </button>
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition">
                                Confirm Deletion
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Alpine.js Client-Side Skin Preview & Instant Head Cropper -->
<script>
function skinUploader() {
    return {
        hasClientPreview: false,
        isInvalid: false,
        errorMessage: '',
        previewLabel: '{{ $user->hasCustomSkin() ? "Custom Head Active" : "Bedrock / Default Icon" }}',

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) {
                this.hasClientPreview = false;
                this.isInvalid = false;
                this.errorMessage = '';
                return;
            }

            if (!file.type.includes('png')) {
                this.isInvalid = true;
                this.errorMessage = 'File must be a PNG image (.png)';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                this.isInvalid = true;
                this.errorMessage = 'Skin file size exceeds 2MB limit.';
                return;
            }

            const img = new Image();
            const url = URL.createObjectURL(file);
            img.onload = () => {
                URL.revokeObjectURL(url);
                if (img.width !== 64 || (img.height !== 64 && img.height !== 32)) {
                    this.isInvalid = true;
                    this.hasClientPreview = false;
                    this.errorMessage = `Invalid skin dimensions (${img.width}x${img.height}). Minecraft skins must be exactly 64x64 or 64x32 pixels.`;
                    return;
                }

                this.isInvalid = false;
                this.errorMessage = '';
                this.previewLabel = '✨ New Skin Selected (Ready to Save)';

                // Draw face + hat onto 64x64 canvas with pixelated rendering
                const canvas = this.$refs.headCanvas;
                const ctx = canvas.getContext('2d');
                ctx.imageSmoothingEnabled = false;
                ctx.clearRect(0, 0, 64, 64);

                // 1. Draw base face from (8, 8) 8x8 -> (0, 0) 64x64
                ctx.drawImage(img, 8, 8, 8, 8, 0, 0, 64, 64);
                // 2. Draw outer hat layer from (40, 8) 8x8 -> (0, 0) 64x64
                ctx.drawImage(img, 40, 8, 8, 8, 0, 0, 64, 64);

                this.hasClientPreview = true;
            };

            img.onerror = () => {
                URL.revokeObjectURL(url);
                this.isInvalid = true;
                this.hasClientPreview = false;
                this.errorMessage = 'Failed to parse image file.';
            };

            img.src = url;
        }
    };
}

function inGameVerifier() {
    return {
        code: null,
        command: '',
        expiresIn: 900,
        countdownText: '15:00',
        loading: false,
        copied: false,
        justVerified: false,
        pollInterval: null,
        timerInterval: null,

        requestCode() {
            this.loading = true;
            fetch('{{ route("profile.verify.generate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    this.code = data.code;
                    this.command = data.command;
                    this.expiresIn = data.expires_in_seconds;
                    this.startCountdown();
                    this.startPolling();
                }
            })
            .catch(() => {
                this.loading = false;
                alert('Could not generate verification code. Please refresh and try again.');
            });
        },

        copyCommand() {
            navigator.clipboard.writeText(this.command);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },

        startCountdown() {
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.updateCountdownText();
            this.timerInterval = setInterval(() => {
                this.expiresIn--;
                if (this.expiresIn <= 0) {
                    clearInterval(this.timerInterval);
                    clearInterval(this.pollInterval);
                    this.code = null;
                }
                this.updateCountdownText();
            }, 1000);
        },

        updateCountdownText() {
            const total = Math.max(0, Math.floor(Number(this.expiresIn) || 0));
            const m = Math.floor(total / 60);
            const s = total % 60;
            this.countdownText = `${m}:${s < 10 ? '0' : ''}${s}`;
        },

        startPolling() {
            if (this.pollInterval) clearInterval(this.pollInterval);
            this.pollInterval = setInterval(() => {
                fetch('{{ route("profile.verify.status") }}', {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.is_verified) {
                        clearInterval(this.pollInterval);
                        clearInterval(this.timerInterval);
                        this.justVerified = true;
                        setTimeout(() => window.location.reload(), 1500);
                    }
                })
                .catch(() => {});
            }, 3000);
        }
    };
}
</script>
@endsection
