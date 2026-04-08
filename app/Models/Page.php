<?php

namespace App\Models;

use App\CMS\ContentResolver;
use App\Enums\PageStatus;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'published_at' => 'datetime',
            'status' => PageStatus::class,
            'metatags' => 'array',
        ];
    }

    public function publish(): void
    {
        $this->status = PageStatus::PUBLISHED;
        $this->published_at = now();
        $this->save();
    }

    public function unpublish(): void
    {
        $this->status = PageStatus::DRAFT;
        $this->published_at = null;
        $this->save();
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class)
            ->using(PageSection::class)
            ->withPivot('order_column');
    }

    public function hasSections(): bool
    {
        return $this->sections()->exists();
    }

    public function scopePublished($query)
    {
        return $query->where('status', PageStatus::PUBLISHED);
    }

    public static function bySlug($slug)
    {
        $cacheKey = 'page.id.'.$slug;

        $pageId = Cache::remember($cacheKey, now()->addDay(), function () use ($slug) {
            return self::where('slug', $slug)->published()->value('id') ?? throw new ModelNotFoundException;
        });

        return self::with(['sections' => function ($query) {
            $query->orderBy('order_column');
        }])->published()->findOrFail($pageId);
    }

    public function sectionsForUI(array $transformables = []): ?array
    {
        $sectionsKeyed = $this->sections->keyBy('slug');

        $sectionsMapped = $sectionsKeyed->map(function ($section, $key) use ($transformables) {

            $contentResolver = new ContentResolver($section);

            return [
                'title' => $section->title,
                'slug' => $section->slug,
                'content' => $contentResolver->resolve($transformables),
            ];
        });

        return $sectionsMapped->all();
    }
}
