<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'layouts' => $page->layout()->toResolvedLayouts()->toArray(),
  'address' => $page->address()->kirbytext()->value(),
  'email' => $page->email()->value(),
  'phone' => $page->phone()->value(),
  'social' => $page->social()->toStructure()->toArray()
];

echo \Kirby\Data\Json::encode($data);
