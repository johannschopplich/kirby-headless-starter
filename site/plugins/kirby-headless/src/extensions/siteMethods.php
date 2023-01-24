<?php

use Kirby\Toolkit\Str;

return [
    'frontendUrl' => function () {
        /** @var \Kirby\Cms\Site $this */
        $url = $this->kirby()->option('headless.panel.frontendUrl');

        if (empty($url) || !$this->kirby()->multilang()) {
            return $url;
        }

        return Str::replace(
            $this->url(),
            $this->kirby()->url(),
            $url
        );
    }
];
