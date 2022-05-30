<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'cover' => ($i = $page->cover()->toFile()) ? $i->toArray() : null,
  'headline' => $page->headline()->value(),
  'subheadline' => $page->subheadline()->value(),
  'text' => $page->text()->value(),
  'tags' => $page->tags()->split()
];

echo \Kirby\Data\Json::encode($data);
