<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'cover' => $page->cover()->toFile()?->toArray(),
  'headline' => $page->headline()->value(),
  'subheadline' => $page->subheadline()->value(),
  'text' => $page->text()->value(),
  'tags' => $page->tags()->split()
];

echo \Kirby\Data\Json::encode($data);
