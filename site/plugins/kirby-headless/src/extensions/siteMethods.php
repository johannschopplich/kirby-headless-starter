<?php

return [
    'frontendUrl' => function () {
        /** @var \Kirby\Cms\Site $this */
        return $this->kirby()->option('headless.panel.frontendUrl');
    }
];
