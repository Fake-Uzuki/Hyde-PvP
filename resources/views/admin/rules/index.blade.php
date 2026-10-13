@extends('admin.layout', ['title' => 'Manage Server Rules'])

@section('admin_content')
<div class="space-y-8" x-data="{ addingRule: false, editingId: null }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white font-gaming">Official Server Rules</h2>
            <p class="text-xs text-slate-400">Rules must be managed by authorized administrators.</p>
        </div>
        <button type="button" @click="addingRule = !addingRule" class="btn-primary text-xs py-2 px-4">
            <span x-text="addingRule ? 'Close Form' : '+ Add New Rule'">+ Add New Rule</span>
        </button>
    </div>

    <!-- Add Rule Form -->
    <div x-show="addingRule" x-transition class="gaming-card p-6 border-purple-500/40">
        <h3 class="text-base font-bold text-white font-gaming mb-4">Add New Server Rule</h3>
        <form method="POST" action="{{ route('admin.rules.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Category</label>
                    <input type="text" name="category" required placeholder="e.g. Gameplay & PvP"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-purple-500" />
                </div>
                <div>
                    <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Title</label>
                    <input type="text" name="title" required placeholder="e.g. No Auto-Clickers"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-purple-500" />
                </div>
                <div>
                    <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Display Order</label>
                    <input type="number" name="order" value="0" min="0"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-purple-500" />
                </div>
            </div>

            <div>
                <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Rule Description</label>
                <textarea name="description" rows="3" required placeholder="Provide the detailed explanation and punishment terms..."
                          class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-purple-500"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="addingRule = false" class="btn-secondary text-xs py-2 px-4">Cancel</button>
                <button type="submit" class="btn-primary text-xs py-2 px-5">Save Rule</button>
            </div>
        </form>
    </div>

    <!-- Rules List -->
    @if($rules->isEmpty())
        <div class="gaming-card p-12 text-center text-slate-500">
            No rules registered yet. Click "+ Add New Rule" to create one.
        </div>
    @else
        <div class="space-y-4">
            @foreach($rules as $rule)
                <div class="gaming-card p-5 space-y-3" x-data="{ editing: false }">
                    <div x-show="!editing">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-purple-950/60 border border-purple-800/40 text-purple-300 text-[10px] font-bold uppercase">
                                        {{ $rule->category }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-mono">Order: {{ $rule->order }}</span>
                                </div>
                                <h3 class="text-base font-bold text-white font-gaming mt-1">{{ $rule->title }}</h3>
                                <p class="text-xs text-slate-300 mt-1 leading-relaxed">{{ $rule->description }}</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="editing = true"
                                        class="text-xs text-purple-400 hover:text-purple-300 px-2.5 py-1 rounded bg-purple-950/40 border border-purple-800/40">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.rules.destroy', $rule->id) }}" onsubmit="return confirm('Delete this rule?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-rose-400 hover:text-rose-300 px-2.5 py-1 rounded bg-rose-950/40 border border-rose-800/40">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <div x-show="editing" style="display: none;">
                        <form method="POST" action="{{ route('admin.rules.update', $rule->id) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Category</label>
                                    <input type="text" name="category" value="{{ $rule->category }}" required
                                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2 text-sm text-white" />
                                </div>
                                <div>
                                    <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Title</label>
                                    <input type="text" name="title" value="{{ $rule->title }}" required
                                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2 text-sm text-white" />
                                </div>
                                <div>
                                    <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Display Order</label>
                                    <input type="number" name="order" value="{{ $rule->order }}" min="0"
                                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2 text-sm text-white" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Description</label>
                                <textarea name="description" rows="3" required
                                          class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2 text-sm text-white">{{ $rule->description }}</textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="editing = false" class="btn-secondary text-xs py-1.5 px-3">Cancel</button>
                                <button type="submit" class="btn-primary text-xs py-1.5 px-4">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
