<?php

use Kirby\Http\Uri;

return [
    'frontendUrl' => function () {
        /** @var \Kirby\Cms\Page $this */
        $frontendUrl = env('KIRBY_HEADLESS_FRONTEND_URL');

        if (empty($frontendUrl)) {
            return null;
        }

        $url = new Uri($frontendUrl);
        $url->path = $this->id();

        return $url->toString();
    }
];
