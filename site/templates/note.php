<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'subheading' => $page->subheading()->value(),
  'tags' => $page->tags()->split(','),
  'text' => $page->text()->toBlocks()->toArray(),
  'published' => $page->date()->toDate('c'),
  'cover' => [
    'url' => ($page->cover()->toFile()?->toArray() ?? $page->images()->first()?->toArray() ?? [])['url'] ?? '',
  ],
  'images' => $page
    ->images()
    ->map(fn ($i) => [
      'id' => $i->id(),
      'uuid' => $i->uuid()->toString(),
      'url' => $i->url(),
      'alt' => $i->alt()->value()
    ])
    ->values()
];

echo \Kirby\Data\Json::encode($data);
