<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'children' => $page
    ->children()
    ->listed()
    ->map(function ($album) {
      $cover = $album->cover()->toFile() ?? $album->images()->first();

      return [
        'id' => $album->id(),
        'title' => $album->title()->value(),
        'cover' => [
          'url' => $cover?->crop(400, 500)->url() ?? '',
          'alt' => $cover?->alt()->value()
        ]
      ];
    })
    ->values()
];

echo \Kirby\Data\Json::encode($data);
