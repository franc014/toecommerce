<?php

namespace App\Traits;

use App\Enums\ProductStatus;

trait Publishable
{
    public function publish(): void
    {
        $this->update(['status' => ProductStatus::ACTIVE, 'published_at' => now()]);
    }

    public function unpublish(): void
    {
        $this->update(['status' => ProductStatus::DRAFT, 'published_at' => null]);
    }

    public function scopePublished($query)
    {
        return $query->where('status', ProductStatus::ACTIVE)->whereNotNull('published_at');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', ProductStatus::DRAFT);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', ProductStatus::ARCHIVED)->whereNotNull('archived_at');
    }

    public function archive(): void
    {
        $this->update(['status' => ProductStatus::ARCHIVED, 'archived_at' => now()]);
    }
}
