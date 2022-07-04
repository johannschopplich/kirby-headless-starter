<?php

return [
    'frontendUrl' => function () {
        /** @var \Kirby\Cms\Site $this */
        $frontendUrl = env('KIRBY_HEADLESS_FRONTEND_URL');

        if (empty($frontendUrl)) {
            return null;
        }

        return $frontendUrl;
    }
];
