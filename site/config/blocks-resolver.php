<?php

use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Cms\Html;
use Kirby\Content\Field;

return [
    'files' => [
        'image' => 'image',
        'gallery' => 'images',
        'video' => 'poster'
    ],

    // Custom resolvers for `block:field`
    'resolvers' => [
        // Resolve permalinks (containing UUIDs) to URLs inside the
        // field `text` of the `prose` block
        'text:text' => function (Field $field, Block $block) {
            return $field->resolvePermalinks()->value();
        },

        'markdown:text' => function (Field $field, Block $block) {
            return $field->kirbytext()->value();
        },

        // A video file has none of the image data the default files resolver adds.
        'video:video' => function (Field $field, Block $block) {
            return $field->toFiles()->values(fn (File $file) => [
                'url' => $file->url(),
                'mime' => $file->mime()
            ]);
        },

        // Adds `embed`, a key the block itself doesn't have.
        'video:embed' => function (Field $field, Block $block) {
            if ($block->location()->value() === 'kirby' || $block->url()->isEmpty()) {
                return null;
            }

            return Html::video($block->url()->value());
        }
    ]
];
