<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'minecraft_gamertag', 'skin_path', 'avatar_head_path'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
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
        return in_array($this->role, ['admin', 'moderator'], true);
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
