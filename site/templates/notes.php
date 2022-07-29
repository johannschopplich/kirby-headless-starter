<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'text' => $page->text()->kirbytext()->value(),
  'children' => $page
    ->children()
    ->listed()
    ->sortBy('date', 'desc')
    ->map(fn ($note) => [
      'id' => $note->id(),
      'title' => $note->title()->value(),
      'tags' => $note->tags()->split(','),
      'text' => $note->text()->toBlocks()->excerpt(280),
      'published' => $note->date()->value(),
      'cover' => [
        'url' => ($note->cover()->toFile()?->toArray() ?? $note->images()->first()?->toArray() ?? [])['url'] ?? '',
      ]
    ])
    ->values()
];

echo \Kirby\Data\Json::encode($data);
