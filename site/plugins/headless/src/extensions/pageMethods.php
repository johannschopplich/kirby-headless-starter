<?php

use Kirby\Toolkit\Str;

return [
    'frontendUrl' => function () {
        /** @var \Kirby\Cms\Page $this */
        $frontendUrl = env('KIRBY_HEADLESS_FRONTEND_URL');

        if (empty($frontendUrl)) {
            return null;
        }

        return Str::replace(
            $this->url(),
            $this->kirby()->url(),
            $frontendUrl
        );
    }
];
