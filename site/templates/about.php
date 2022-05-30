<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'layout' => $page->layout()->toLayouts()->toArray(),
  'address' => $page->address()->value(),
  'email' => $page->email()->value(),
  'phone' => $page->phone()->value(),
  'social' => $page->social()->toStructure()->toArray()
];

echo \Kirby\Data\Json::encode($data);
