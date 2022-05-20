<?php

namespace KirbyHeadless\Api;

use Kirby\Cms\Url;
use Kirby\Http\Response;

class Api
{
    protected static string $slug;

    /**
     * Get the API slug for use in a routing pattern
     * Note: unused for now
     *
     * @return string
     */
    public static function useSlug(): string
    {
        return static::$slug ??= Url::path(env('KIRBY_HEADLESS_API_SLUG', ''));
    }

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
