@extends('admin.layout', ['title' => 'Super Admin: User Accounts & Roles Control'])

@section('admin_content')
<div class="space-y-6" x-data="{
    passwordModal: false,
    emailModal: false,
    selectedUser: null,
    openPasswordModal(user) {
        this.selectedUser = user;
        this.passwordModal = true;
    },
    openEmailModal(user) {
        this.selectedUser = user;
        this.emailModal = true;
    }
}">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-950/60 border border-purple-500/40 text-purple-300 text-xs font-semibold mb-2">
                <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Super Administrator Authority
            </div>
            <h2 class="text-xl font-bold text-white font-gaming">User Accounts & Role Management</h2>
            <p class="text-xs text-slate-400">Promote admins, recover hacked accounts, force reset passwords, and manage player bans.</p>
        </div>

        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, gamertag..."
                   class="bg-[#070A12] border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-purple-500 w-52" />

            <select name="role" class="bg-[#070A12] border border-slate-800 rounded-xl px-2.5 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-purple-500">
                <option value="">All Roles</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>Moderator</option>
                <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Member</option>
            </select>

            <select name="status" class="bg-[#070A12] border border-slate-800 rounded-xl px-2.5 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-purple-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified In-Game</option>
            </select>

            <button type="submit" class="btn-secondary text-xs py-1.5 px-3">Filter</button>
            @if(request('search') || request('role') || request('status'))
                <a href="{{ route('admin.users.index') }}" class="text-xs text-slate-500 hover:text-white">Clear</a>
            @endif
        </form>
    </div>

    @if($users->isEmpty())
        <div class="gaming-card p-12 text-center text-slate-500">
            No user accounts matched your search criteria.
        </div>
    @else
        <div class="gaming-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3.5 px-5 font-bold">User Account</th>
                            <th class="py-3.5 px-5 font-bold">Email</th>
                            <th class="py-3.5 px-5 font-bold">Bedrock Gamertag</th>
                            <th class="py-3.5 px-5 font-bold">Status</th>
                            <th class="py-3.5 px-5 font-bold">Role Assignment</th>
                            <th class="py-3.5 px-5 font-bold text-right">Account Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-900/40 transition {{ $user->is_suspended ? 'bg-rose-950/10' : '' }}">
                                <td class="py-3.5 px-5 font-bold text-white">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-7 h-7 rounded bg-slate-800 object-cover border border-slate-700/60" />
                                        <div>
                                            <span class="block">{{ $user->name }}</span>
                                            <span class="text-[10px] text-slate-500 font-normal">Joined {{ $user->created_at->format('M d, Y') }}</span>
                                        </div>
                                        @if($user->id === Auth::id())
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-900/40 border border-purple-700/40 text-purple-300 font-mono">You</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-300 font-mono">
                                    {{ $user->email }}
                                </td>
                                <td class="py-3.5 px-5 text-xs font-mono">
                                    @if($user->minecraft_gamertag)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-cyan-300">{{ $user->minecraft_gamertag }}</span>
                                            @if($user->is_verified)
                                                <span class="text-[9px] px-1.5 py-0.2 rounded bg-cyan-950 border border-cyan-800/60 text-cyan-300 uppercase font-bold">Verified</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5">
                                    @if($user->is_suspended)
                                        <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wider font-mono bg-rose-950/70 text-rose-300 border border-rose-800/50">
                                            Suspended
                                        </span>
                                    @else
                                        <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wider font-mono bg-emerald-950/70 text-emerald-300 border border-emerald-800/50">
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5">
                                    <form method="POST" action="{{ route('admin.users.role', $user->id) }}" class="inline-flex items-center gap-1.5"
                                          onsubmit="return confirm('Change role for {{ $user->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="bg-[#070A12] border border-slate-800 rounded-lg px-2 py-1 text-xs text-white focus:outline-none focus:border-purple-500 font-mono">
                                            <option value="member" {{ $user->role === 'member' ? 'selected' : '' }}>member</option>
                                            <option value="moderator" {{ $user->role === 'moderator' ? 'selected' : '' }}>moderator</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>admin</option>
                                            <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>super_admin</option>
                                        </select>
                                        <button type="submit" class="btn-secondary text-[11px] py-1 px-2.5">
                                            Set
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <!-- Reset Password -->
                                        <button type="button"
                                                @click="openPasswordModal({ id: {{ $user->id }}, name: '{{ addslashes($user->name) }}', email: '{{ addslashes($user->email) }}' })"
                                                class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-medium transition"
                                                title="Reset password for locked or hacked account">
                                            Reset Pass
                                        </button>

                                        <!-- Edit Email -->
                                        <button type="button"
                                                @click="openEmailModal({ id: {{ $user->id }}, name: '{{ addslashes($user->name) }}', email: '{{ addslashes($user->email) }}' })"
                                                class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-medium transition"
                                                title="Update account email">
                                            Email
                                        </button>

                                        <!-- Force Unlink Gamertag -->
                                        @if($user->minecraft_gamertag)
                                            <form method="POST" action="{{ route('admin.users.unlink-gamertag', $user->id) }}" class="inline"
                                                  onsubmit="return confirm('Force unlink Minecraft gamertag {{ $user->minecraft_gamertag }} from {{ $user->name }}?')">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 rounded bg-amber-950/60 hover:bg-amber-900 border border-amber-800/40 text-amber-300 text-[11px] transition" title="Unlink Minecraft Gamertag">
                                                    Unlink GT
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Suspend / Reinstate -->
                                        @if($user->id !== Auth::id() && !$user->isSuperAdmin())
                                            <form method="POST" action="{{ route('admin.users.toggle-suspension', $user->id) }}" class="inline"
                                                  onsubmit="return confirm('{{ $user->is_suspended ? 'Reinstate' : 'Suspend' }} account {{ $user->name }}?')">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 rounded text-[11px] transition {{ $user->is_suspended ? 'bg-emerald-950/60 hover:bg-emerald-900 border border-emerald-800/40 text-emerald-300' : 'bg-rose-950/60 hover:bg-rose-900 border border-rose-800/40 text-rose-300' }}">
                                                    {{ $user->is_suspended ? 'Unsuspend' : 'Suspend' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-4">
            {{ $users->links() }}
        </div>
    @endif

    <!-- Super Admin: Password Reset Modal -->
    <div x-show="passwordModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="gaming-card p-6 max-w-md w-full space-y-4" @click.outside="passwordModal = false">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white font-gaming flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Force Reset Password
                </h3>
                <button type="button" @click="passwordModal = false" class="text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                Resetting password for: <strong class="text-purple-300" x-text="selectedUser?.name"></strong> (<span class="font-mono text-[11px]" x-text="selectedUser?.email"></span>)
            </p>

            <form :action="'{{ url('admin/users') }}/' + selectedUser?.id + '/password'" method="POST" class="space-y-3">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">New Temporary Password *</label>
                    <input type="password" name="new_password" required minlength="8" placeholder="Minimum 8 characters"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500 font-mono" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Confirm New Password *</label>
                    <input type="password" name="new_password_confirmation" required minlength="8" placeholder="Retype password"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500 font-mono" />
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="passwordModal = false" class="btn-secondary text-xs py-2 px-4">Cancel</button>
                    <button type="submit" class="btn-primary text-xs py-2 px-5">Save New Password</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Super Admin: Email Change Modal -->
    <div x-show="emailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="gaming-card p-6 max-w-md w-full space-y-4" @click.outside="emailModal = false">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white font-gaming flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Update Account Email
                </h3>
                <button type="button" @click="emailModal = false" class="text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                Updating email address for: <strong class="text-purple-300" x-text="selectedUser?.name"></strong>
            </p>

            <form :action="'{{ url('admin/users') }}/' + selectedUser?.id + '/email'" method="POST" class="space-y-3">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">New Verified Email Address *</label>
                    <input type="email" name="email" required :value="selectedUser?.email"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500 font-mono" />
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="emailModal = false" class="btn-secondary text-xs py-2 px-4">Cancel</button>
                    <button type="submit" class="btn-primary text-xs py-2 px-5">Update Email</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
