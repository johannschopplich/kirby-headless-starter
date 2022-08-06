<?php

/** @var \Kirby\Cms\App $kirby */
/** @var \Kirby\Cms\Site $site */

$data = [
  'children' => $site
    ->children()
    ->published()
    ->map(fn ($child) => [
        'id' => $child->id(),
        'title' => $child->title()->value(),
        'template' => $child->intendedTemplate()->name()
    ])
    ->values()
];

echo \Kirby\Data\Json::encode($data);
