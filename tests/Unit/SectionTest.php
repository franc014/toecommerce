<?php

use App\Models\Section;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    $this->content = [
        [
            'type' => 'heading',
            'data' => [
                'content' => 'Heading 1',
                'level' => 'h1',
            ],
        ],
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
                'content' => 'Paragraph 1',
            ],
        ],
        [
            'type' => 'paragraph',
            'data' => [
                'content' => 'Paragraph 2',
            ],
        ],

    ];

});
test('can resolve content', function () {

    $section = Section::factory()->create(
        [
             'content' => $this->content,
         ]
    );

    $resolved = $section->resolveContent();

    expect($resolved['heading'][0]['content'])->toBe('Heading 1');
    expect($resolved['paragraph'][0]['content'])->toBe('Paragraph 1');
    expect($resolved['heading'][1]['content'])->toBe('Heading 2');
    expect($resolved['paragraph'][1]['content'])->toBe('Paragraph 2');
});

test('can include images in resolved content', function () {

    Storage::fake('local');

    $section = Section::factory()->create([
        'content' => $this->content,
    ]);

    // add fake spatie images for section

    $section->addMediaFromUrl('https://picsum.photos/200')->toMediaCollection('gallery-A', 'local');
    $section->addMediaFromUrl('https://picsum.photos/300')->toMediaCollection('gallery-B', 'local');

    $section->addMediaFromUrl('https://picsum.photos/400')->toMediaCollection('gallery-A', 'local');
    $section->addMediaFromUrl('https://picsum.photos/500')->toMediaCollection('gallery-B', 'local');


    $images = $section->resolveContent()['images'];

    expect($images)->toHaveCount(4);
    expect($images[0])->toBeInstanceOf(Media::class);
    expect($images[0])->toBeInstanceOf(Media::class);

    // conversions: thumb
    expect($images[0]->getUrl('thumb'))->toBe('/storage/1/conversions/200-thumb.jpg');
    expect($images[1]->getUrl('thumb'))->toBe('/storage/2/conversions/300-thumb.jpg');

    expect($images[2]->getUrl('thumb'))->toBe('/storage/3/conversions/400-thumb.jpg');
    expect($images[3]->getUrl('thumb'))->toBe('/storage/4/conversions/500-thumb.jpg');

});
