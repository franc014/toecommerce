<?php

namespace App\Models;

use App\Enums\SectionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Section extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\SectionFactory> */
    use HasFactory,InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'content' => 'array',
            'status' => SectionStatus::class,
        ];
    }

    public function activate(): void
    {
        $this->status = SectionStatus::ACTIVE;
        $this->save();
    }

    public function deactivate(): void
    {
        $this->status = SectionStatus::INACTIVE;
        $this->save();
    }

    public function registerAllMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->fit(Fit::Contain, 300, 300);
    }

    public function getImages(): ?MediaCollection
    {
        return $this->hasMedia('*') ? $this->getMedia('*') : null;
    }

    public function resolveContent()
    {

        $contentByType = collect($this->content)->groupBy('type');

        $content = $contentByType->map(function ($content) {
            return $content->pluck('data')->all();
        });

        return array_merge($content->toArray(), ['images' => $this->getImages()]);
    }

}
