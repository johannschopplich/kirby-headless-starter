<?php

use Kirby\Toolkit\Str;

return [
    'frontendUrl' => function () {
        /** @var \Kirby\Cms\Page $this */
        $frontendUrl = $this->kirby()->option('headless.panel.frontendUrl');

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
