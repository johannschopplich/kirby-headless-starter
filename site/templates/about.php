<?php

/** @var \Kirby\Cms\Page $page */

$data = [
  '_meta' => [
    'template' => $page->intendedTemplate()->name(),
    'isHomePage' => $page->isHomePage(),
    'isErrorPage' => $page->isErrorPage()
  ],
  'title' => $page->title()->value(),
  'layout' => $page->layout()->toLayouts()->map(fn ($layout) => $layout->toArray())->values(),
  'address' => $page->address()->value(),
  'email' => $page->email()->value(),
  'phone' => $page->phone()->value(),
  'social' => $page->social()->toStructure()->map(fn ($social) => $social->toArray())->values()
];

echo toJson($data);
