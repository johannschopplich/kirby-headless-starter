<?php

namespace KirbyHeadless\Api;

class UserMiddlewares
{
    /**
     * Redirect to the Kirby Panel if no
     * authorization header is provided
     *
     * @return void
     */
    public static function hasAuthHeaderOrRedirect()
    {
        $authorization = kirby()->request()->header('Authorization');

        if (empty($authorization)) {
            go(option('panel.slug'));
        }
    }
}