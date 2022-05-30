<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'children' => $page
    ->children()
    ->listed()
    ->toArray()
];

echo \Kirby\Data\Json::encode($data);
