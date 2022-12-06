<?php

$imageBlockResolver = function (\Kirby\Cms\Block $block) {
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
};

return [
    /**
     * Enhances the `toBlocks()` method to resolve images
     *
     * @kql-allowed
     */
    'toResolvedBlocks' => function (\Kirby\Cms\Field $field) use ($imageBlockResolver) {
        return $field
            ->toBlocks()
            ->map($imageBlockResolver);
    },

    /**
     * Enhances the `toLayouts()` method to resolve images
     *
     * @kql-allowed
     */
    'toResolvedLayouts' => function (\Kirby\Cms\Field $field) use ($imageBlockResolver) {
        return $field
            ->toLayouts()
            ->map(function (\Kirby\Cms\Layout $layout) use ($imageBlockResolver) {
                $columns = $layout->columns();

                foreach ($columns as $column) {
                    $column->blocks()->map($imageBlockResolver);
                }

                return $layout;
            });
    }
];
