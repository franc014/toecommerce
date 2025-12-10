<?php

use App\Models\Page;
use App\Models\Section;

test('can extract all content for a published page', function () {

    $section1 = Section::factory()->create([
        'slug' => 'hero',
        'content' => [
            [
                'type' => 'heading',
                'data' => [
                    'content' => 'Heading 1',
                    'level' => 'h1',
                ],
            ],
            [
                'type' => 'paragraph',
                'data' => [
                    'content' => 'Paragraph 1',
                ],
            ],
        ],
    ]);

    $section2 = Section::factory()->create([
        'slug' => 'values',
        'content' => [
            [
                'type' => 'heading',
                'data' => [
                    'content' => 'Heading 2',
                    'level' => 'h2',
                ],
            ],
            [
                'type' => 'paragraph',
                'data' => [
                    'content' => 'Paragraph 2',
                ],
            ],

        ],
    ]);

    $page = Page::factory()->published()->create([
        'slug' => 'home',
    ]);

    $page->sections()->attach([$section1->id, $section2->id]);

    $sections = Page::bySlug('home')->sectionsForUI();

    $sc = [
        'hero' => [
            'title' => $section1->title,
            'slug' => $section1->slug,
            'content' => [
                'heading' => [[
                    'content' => 'Heading 1',
                    'level' => 'h1',
                ]],
                'paragraph' => [[
                    'content' => 'Paragraph 1',
                ]],
                'images' => null,
            ],

        ],
        'values' => [
            'title' => $section2->title,
            'slug' => $section2->slug,
            'content' => [
                'heading' => [[
                    'content' => 'Heading 2',
                    'level' => 'h2',
                ]],
                'paragraph' => [[
                    'content' => 'Paragraph 2',
                ]],
                'images' => null,
            ],

        ],
    ];

    expect($sections['hero']['title'])->toEqual($sc['hero']['title']);
    expect($sections['hero']['slug'])->toEqual($sc['hero']['slug']);
    expect($sections['hero']['content'])->toEqual($sc['hero']['content']);

});
