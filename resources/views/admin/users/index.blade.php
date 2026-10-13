@extends('admin.layout', ['title' => 'User Role Management'])

@section('admin_content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white font-gaming">User Roles & Access Control</h2>
            <p class="text-xs text-slate-400">Strict server-side RBAC: promote users to Moderator or Admin, or demote to Member.</p>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, gamertag..."
                   class="bg-[#070A12] border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-purple-500 w-56" />
            <button type="submit" class="btn-secondary text-xs py-1.5 px-3">Filter</button>
            @if(request('search'))
                <a href="{{ route('admin.users.index') }}" class="text-xs text-slate-500 hover:text-white">Clear</a>
            @endif
        </form>
    </div>

    @if($users->isEmpty())
        <div class="gaming-card p-12 text-center text-slate-500">
            No users matched your search criteria.
        </div>
    @else
        <div class="gaming-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-5 font-bold">User</th>
                            <th class="py-3 px-5 font-bold">Email</th>
                            <th class="py-3 px-5 font-bold">Bedrock Gamertag</th>
                            <th class="py-3 px-5 font-bold">Registered</th>
                            <th class="py-3 px-5 font-bold">Current Role</th>
                            <th class="py-3 px-5 font-bold text-right">Assign Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-900/40 transition">
                                <td class="py-3 px-5 font-bold text-white flex items-center gap-2">
                                    <div class="w-7 h-7 rounded bg-gradient-to-tr from-purple-700 to-fuchsia-600 flex items-center justify-center font-bold text-white text-[10px]">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    {{ $user->name }}
                                    @if($user->id === Auth::id())
                                        <span class="text-[10px] text-purple-400 font-mono">(You)</span>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-xs text-slate-300 font-mono">
                                    {{ $user->email }}
                                </td>
                                <td class="py-3 px-5 text-xs font-mono">
                                    @if($user->minecraft_gamertag)
                                        <span class="text-cyan-300">{{ $user->minecraft_gamertag }}</span>
                                        <span class="text-[10px] text-slate-500">(unverified)</span>
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-xs text-slate-400">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-5">
                                    <span class="text-xs px-2.5 py-0.5 rounded font-bold uppercase tracking-wider text-[10px] font-mono
                                        {{ $user->role === 'admin' ? 'bg-purple-950/60 text-purple-300 border border-purple-800/40' :
                                           ($user->role === 'moderator' ? 'bg-blue-950/60 text-blue-300 border border-blue-800/40' :
                                           'bg-slate-800/60 text-slate-400 border border-slate-700/40') }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="py-3 px-5 text-right">
                                    <form method="POST" action="{{ route('admin.users.role', $user->id) }}" class="inline-flex items-center gap-1.5"
                                          onsubmit="return confirm('Update role for {{ $user->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="bg-[#070A12] border border-slate-800 rounded-lg px-2 py-1 text-xs text-white focus:outline-none focus:border-purple-500 font-mono">
                                            <option value="member" {{ $user->role === 'member' ? 'selected' : '' }}>member</option>
                                            <option value="moderator" {{ $user->role === 'moderator' ? 'selected' : '' }}>moderator</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>admin</option>
                                        </select>
                                        <button type="submit" class="btn-secondary text-[11px] py-1 px-2.5">
                                            Update
                                        </button>
                                    </form>
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

</div>
@endsection
