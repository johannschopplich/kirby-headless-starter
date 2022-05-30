<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  'title' => $page->title()->value(),
  'cover' => $page->cover()->toFile()?->toArray(),
  'date' => $page->date()->value(),
  'tags' => $page->tags()->split(),
  'text' => $page->text()->toBlocks()->toArray()
];

echo \Kirby\Data\Json::encode($data);
