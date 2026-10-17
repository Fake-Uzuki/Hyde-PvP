<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class NewsPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'images',
        'video_path',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get accessible URLs for all uploaded images.
     */
    public function getImageUrlsAttribute(): array
    {
        if (empty($this->images) || ! is_array($this->images)) {
            return [];
        }

        return array_map(function ($path) {
            if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }
            return \Illuminate\Support\Facades\Storage::url($path);
        }, $this->images);
    }

    /**
     * Get accessible URL for the uploaded video.
     */
    public function getVideoUrlAttribute(): ?string
    {
        if (empty($this->video_path)) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($this->video_path, ['http://', 'https://'])) {
            return $this->video_path;
        }

        return \Illuminate\Support\Facades\Storage::url($this->video_path);
    }

    /**
     * Extract YouTube Video ID if video_path is a YouTube link.
     */
    public function getYoutubeIdAttribute(): ?string
    {
        if (empty($this->video_path)) {
            return null;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $this->video_path, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get first image URL as featured/thumbnail image.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        $urls = $this->image_urls;
        return !empty($urls) ? $urls[0] : null;
    }

    protected static function booted(): void
    {
        static::creating(function (NewsPost $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title) . '-' . Str::random(5);
            }
            if ($post->is_published && empty($post->published_at)) {
                $post->published_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function visibleComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('is_hidden', false)->latest();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->whereNotNull('published_at');
    }
}
