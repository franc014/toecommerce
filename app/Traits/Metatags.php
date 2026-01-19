<?php

namespace App\Traits;

trait Metatags
{
    public function metatags()
    {

        return [
            'title' => $this->title(),
            'description' => $this->description(),
            'og_title' => $this->og_title(),
            'og_description' => $this->og_description(),
            'og_image' => $this->og_image(),
            'twitter_card' => $this->twitter_card(),
            'twitter_title' => $this->twitter_title(),
            'twitter_description' => $this->twitter_description(),
            'twitter_image' => $this->twitter_image(),
            'robots' => $this->robots(),
            'keywords' => $this->keywords(),
            'schemaOrg' => $this->schema_org(),
        ];

    }

    private function title()
    {
        return $this->page->metatags['title'] ?? $this->page->title ?? '';
    }

    private function description()
    {
        return $this->page->metatags['description'] ?? $this->page->description ?? '';
    }

    private function og_title()
    {
        return $this->page->metatags['og_title'] ?? $this->title();
    }

    private function og_description()
    {
        return $this->page->metatags['og_description'] ?? $this->description();
    }

    private function og_image()
    {
        return $this->page->metatags['og_image'] ?? config('app.seo.defaults.og_image') ?? '';
    }

    private function twitter_card()
    {
        return $this->page->metatags['twitter_card'] ?? 'summary_large_image';
    }

    private function twitter_title()
    {
        return $this->page->metatags['twitter_title'] ?? $this->title();
    }

    private function twitter_description()
    {
        return $this->page->metatags['twitter_description'] ?? $this->description();
    }

    private function twitter_image()
    {
        return $this->page->metatags['twitter_image'] ?? config('app.seo.defaults.twitter_image') ?? '';
    }

    private function keywords()
    {
        return $this->page->metatags['keywords'] ?? '';
    }

    private function robots()
    {
        return $this->page->metatags['robots'] ?? 'index,follow';
    }

    private function schema_org()
    {
        return null;
    }


}
