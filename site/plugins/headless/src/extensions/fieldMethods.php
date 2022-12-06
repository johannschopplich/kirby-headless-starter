<?php

return [
    /**
     * Enhances the `toBlocks()` method to resolve images
     *
     * @kql-allowed
     */
    'toResolvedBlocks' => function (\Kirby\Cms\Field $field) {
        return $field
            ->toBlocks()
            ->map(function ($block) {
                /** @var \Kirby\Cms\Block $block */
                if ($block->type() === 'image') {
                    /** @var \Kirby\Cms\File|null $image */
                    $image = $block->content()->get('image')->toFile();

                    if ($image) {
                        $resolvedImage = [
                            'url'    => $image->url(),
                            'width'  => $image->width(),
                            'height' => $image->height(),
                            'srcset' => $image->srcset(),
                            'alt'    => $image->alt()->value()
                        ];

                        // Replace the image field with the resolved image
                        $block->content()->update(['image' => $resolvedImage]);
                    }
                }

                return $block;
            });
    }
];
