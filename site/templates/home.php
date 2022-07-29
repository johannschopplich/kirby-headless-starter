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
    ->map(fn ($album) => [
      'id' => $album->id(),
      'title' => $album->title()->value(),
      'cover' => [
        'url' => ($album->cover()->toFile()?->resize(1024, 1024)?->toArray() ?? $page->images()->first()?->resize(1024, 1024)?->toArray() ?? [])['url'] ?? '',
        'alt' => $album->cover()->toFile()?->alt()->value()
      ]
    ])
    ->values()
];

echo \Kirby\Data\Json::encode($data);
