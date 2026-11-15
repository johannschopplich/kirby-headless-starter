<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'subheading' => $page->subheading()->value(),
  'tags' => $page->tags()->split(','),
  'text' => $page->text()->toResolvedBlocks()->toArray(),
  'published' => $page->date()->isNotEmpty() ? date('c', $page->date()->toTimestamp()) : null,
  'cover' => [
    'url' => ($page->cover()->toFile()?->toArray() ?? $page->images()->first()?->toArray() ?? [])['url'] ?? '',
  ]
];

echo \Kirby\Data\Json::encode($data);
