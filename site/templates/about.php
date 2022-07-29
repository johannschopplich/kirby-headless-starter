<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'layouts' => $page->layouts()->toLayouts()->toArray(),
  'address' => $page->address()->kirbytext()->value(),
  'email' => $page->email()->value(),
  'phone' => $page->phone()->value(),
  'social' => $page->social()->toStructure()->toArray(),
  'images' => $page
    ->images()
    ->map(fn ($i) => [
      'id' => $note->id(),
      'filename' => $note->filename(),
      'url' => $note->url(),
      'alt' => $note->alt()
    ])
    ->values()
];

echo \Kirby\Data\Json::encode($data);
