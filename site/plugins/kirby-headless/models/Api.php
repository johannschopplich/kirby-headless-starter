<?php

namespace KirbyHeadless\Api;

use Kirby\Cms\Url;
use Kirby\Http\Response;

class Api
{
    /**
     * Create an API handler
     *
     * @param callable[] $fns
     * @return mixed
     */
    public static function createHandler(callable ...$fns)
    {
        $context = [];

        return function (...$args) use ($fns, $context) {
            foreach ($fns as $fn) {
                $result = $fn($context, $args);

                if (is_a($result, Response::class)) {
                    return $result;
                }

                if (is_array($result)) {
                    $context = $result;
                }
            }
        };
    }
}
