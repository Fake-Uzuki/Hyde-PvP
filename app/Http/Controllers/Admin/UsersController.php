<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\LegitEmail;
use App\Services\PlayerAvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UsersController extends Controller
{
    /**
     * Display a searchable directory of user accounts with super admin control.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('minecraft_gamertag', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'suspended') {
                $query->where('is_suspended', true);
            } elseif ($request->status === 'active') {
                $query->where('is_suspended', false);
            } elseif ($request->status === 'verified') {
                $query->where('is_verified', true);
            }
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Update user RBAC role (Super Admin only).
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['super_admin', 'admin', 'moderator', 'member'])],
        ]);

        // Safety check: Prevent demoting the sole Super Admin
        if ($user->role === 'super_admin' && $validated['role'] !== 'super_admin') {
            $superAdminCount = User::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Action rejected: You cannot demote the sole Super Administrator on the system.');
            }
        }

        $oldRole = $user->role;
        $user->role = $validated['role'];
        $user->save();

        return back()->with('status', "Role for {$user->name} successfully updated from '{$oldRole}' to '{$user->role}'.");
    }

    /**
     * Force reset a user's password (for hacked or locked accounts).
     */
    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return back()->with('status', "Password for account '{$user->name}' ({$user->email}) has been successfully reset by Super Admin.");
    }

    /**
     * Force update a user's email address (for account recovery).
     */
    public function updateEmail(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
                new LegitEmail(),
            ],
        ]);

        $oldEmail = $user->email;
        $user->email = $validated['email'];
        $user->email_verified_at = now(); // Mark verified since Super Admin manually audited
        $user->save();

        return back()->with('status', "Email for {$user->name} updated from '{$oldEmail}' to '{$user->email}'.");
    }

    /**
     * Force unlink a Minecraft Bedrock gamertag (for disputes or errors).
     */
    public function unlinkGamertag(Request $request, User $user): RedirectResponse
    {
        $oldGamertag = $user->minecraft_gamertag;

        if ($oldGamertag) {
            PlayerAvatarService::clearCache($oldGamertag);
        }

        $user->minecraft_gamertag = null;
        $user->xuid = null;
        $user->is_verified = false;
        $user->verified_at = null;
        $user->save();

        return back()->with('status', "Gamertag '{$oldGamertag}' unlinked from {$user->name}'s account.");
    }

    /**
     * Suspend or reinstate an account.
     */
    public function toggleSuspension(Request $request, User $user): RedirectResponse
    {
        // Cannot suspend self or another super admin
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot suspend your own Super Admin account.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Super Administrator accounts cannot be suspended.');
        }

        $user->is_suspended = ! $user->is_suspended;
        if ($user->is_suspended && $request->filled('reason')) {
            $user->suspension_reason = $request->reason;
        } elseif (! $user->is_suspended) {
            $user->suspension_reason = null;
        }

        $user->save();

        $action = $user->is_suspended ? 'suspended' : 'reinstated';
        return back()->with('status', "Account for {$user->name} has been {$action}.");
    }
}
