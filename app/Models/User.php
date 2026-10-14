<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'minecraft_gamertag', 'is_verified', 'xuid', 'verified_at', 'skin_path', 'avatar_head_path', 'is_suspended', 'suspension_reason', 'admin_notes'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'is_verified' => 'boolean',
            'is_suspended' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get user avatar URL (custom uploaded skin head, Bedrock gamertag head, or default).
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar_head_path)) {
            return \Illuminate\Support\Facades\Storage::url($this->avatar_head_path);
        }

        if (!empty($this->minecraft_gamertag)) {
            return "https://mc-heads.net/avatar/" . rawurlencode(trim($this->minecraft_gamertag)) . "/64";
        }

        return "https://mc-heads.net/avatar/MHF_Steve/64";
    }

    /**
     * Get user raw skin PNG URL if uploaded.
     */
    public function getSkinUrlAttribute(): ?string
    {
        if (!empty($this->skin_path)) {
            return \Illuminate\Support\Facades\Storage::url($this->skin_path);
        }

        return null;
    }

    /**
     * Check if user has uploaded a custom Minecraft skin.
     */
    public function hasCustomSkin(): bool
    {
        return !empty($this->avatar_head_path);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin'], true);
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'moderator'], true);
    }

    public function canManageRoles(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }

    public function isSuspended(): bool
    {
        return (bool) $this->is_suspended;
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    public function newsPosts(): HasMany
    {
        return $this->hasMany(NewsPost::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
