<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'headline' => $page->headline()->value(),
  'subheadline' => $page->subheadline()->value()
];

echo \Kirby\Data\Json::encode($data);
