<?php

/** @var \Kirby\Cms\App $kirby */
/** @var \Kirby\Cms\Site $site */

$data = [
  'title' => $site->title()->value(),
  'children' => $site
    ->children()
    ->published()
    ->map(fn ($child) => [
        'id' => $child->id(),
        'title' => $child->title()->value(),
        'isListed' => $child->isListed()
    ])
    ->values()
];

echo \Kirby\Data\Json::encode($data);
