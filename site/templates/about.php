<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'layouts' => $page->layout()->toLayouts()->toArray(),
  'address' => $page->address()->kirbytext()->value(),
  'email' => $page->email()->value(),
  'phone' => $page->phone()->value(),
  'social' => $page->social()->toStructure()->toArray(),
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
