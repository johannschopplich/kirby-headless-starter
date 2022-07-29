<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'subheadline' => $page->subheadline()->value(),
  'text' => $page->text()->value(),
  'gallery' => $page
    ->images()
    ->sortBy('sort', 'filename')
    ->map(fn ($file) => [
      'resized' => [
        'url' => ($file->resize(800)?->toArray() ?? [])['url'] ?? ''
      ],
      'width' => $file->width(),
      'height' => $file->height(),
      'url' => $file->url(),
      'alt' => $file->alt()->value()
    ])
    ->values()
];

echo \Kirby\Data\Json::encode($data);
