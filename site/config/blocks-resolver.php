<?php

use Kirby\Cms\Block;
use Kirby\Content\Field;

return [
    // Custom resolves for `block:field`
    'resolvers' => [
        // Resolve permalinks (containing UUIDs) to URLs inside the
        // field `text` of the `prose` block
        'text:text' => function (Field $field, Block $block) {
            return $field->resolvePermalinks()->value();
        }
    ]
];
