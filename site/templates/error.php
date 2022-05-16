<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  '_meta' => [
    'template' => $page->intendedTemplate()->name(),
    'isHomePage' => $page->isHomePage(),
    'isErrorPage' => $page->isErrorPage()
  ],
  'title' => $page->title()->value()
];

echo toJson($data);
