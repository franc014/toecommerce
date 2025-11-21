<?php

namespace App\Models;

use App\CMS\ContentResolver;
use App\CMS\ImageTransformable;
use App\Enums\PageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
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
        return $this->sections()->count() > 0;
    }

    public function scopePublished($query)
    {
        return $query->where('status', PageStatus::PUBLISHED);
    }

    public static function bySlug($slug)
    {
        /* return Cache::remember('page-'.$slug, now()->addDay(), function () use ($slug) {
            return self::where('slug', $slug)->published()
                ->with('sections', function ($query) {
                    $query->orderBy('order_column');
                })
                ->lazy()->firstOrFail();
        }); */

        return self::where('slug', $slug)->published()
            ->with('sections', function ($query) {
                $query->orderBy('order_column');
            })
            ->lazy()->firstOrFail();

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
