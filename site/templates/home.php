<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'headline' => $page->headline()->value(),
  'subheadline' => $page->subheadline()->value(),
  'children' => $kirby
    ->page('photography')
    ->children()
    ->listed()
    ->map(function ($album) {
      $cover = $album->cover()->toFile() ?? $album->images()->first();

      return [
        'id' => $album->id(),
        'title' => $album->title()->value(),
        'cover' => [
          'url' => $cover?->resize(1024, 1024)->url() ?? '',
          'alt' => $cover?->alt()->value()
        ]
      ];
    })
    ->values()
];

echo \Kirby\Data\Json::encode($data);
