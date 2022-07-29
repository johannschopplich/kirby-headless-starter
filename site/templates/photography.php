<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'children' => $page
    ->children()
    ->listed()
    ->map(fn ($album) => [
      'id' => $album->id(),
      'title' => $album->title()->value(),
      'cover' => [
        'url' => ($album->cover()->toFile()?->crop(400, 500)?->toArray() ?? $page->images()->first()?->crop(400, 500)?->toArray() ?? [])['url'] ?? '',
        'alt' => $album->cover()->toFile()?->alt()->value()
      ]
    ])
    ->values()
];

echo \Kirby\Data\Json::encode($data);
