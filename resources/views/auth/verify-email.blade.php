<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-purple-950/60 border border-purple-500/30 flex items-center justify-center mx-auto text-purple-400 shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-xl font-extrabold text-white font-gaming tracking-wide">
                Verify Your Email
            </h1>
            <p class="text-xs text-slate-400 leading-relaxed">
                Thanks for registering! Please check your inbox and click the verification link we sent to confirm your email address.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="p-3.5 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-xs text-emerald-300 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>A new verification link has been sent to your registered email address.</span>
            </div>
        @endif

        <div class="space-y-3 pt-2">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full btn-primary py-3 text-xs uppercase font-gaming tracking-wider flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Resend Verification Email</span>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="text-center">
                @csrf
                <button type="submit" class="text-xs text-slate-400 hover:text-rose-400 transition font-medium">
                    Log Out of Account
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
