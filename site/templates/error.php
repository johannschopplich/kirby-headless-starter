<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  '__meta' => [
    'template' => $page->intendedTemplate()->name(),
    'isHomePage' => $page->isHomePage(),
    'isErrorPage' => $page->isErrorPage()
  ],
  'title' => $page->title()->value()
];

echo \Kirby\Data\Json::encode($data);
