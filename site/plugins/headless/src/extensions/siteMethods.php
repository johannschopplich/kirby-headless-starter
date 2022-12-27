<?php

return [
    'frontendUrl' => function () {
        /** @var \Kirby\Cms\Site $this */
        return env('KIRBY_HEADLESS_FRONTEND_URL');
    }
];
