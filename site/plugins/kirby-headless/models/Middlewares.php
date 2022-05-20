<?php

namespace KirbyHeadless\Api;

use Exception;
use KirbyHeadless\Api\ApiResponse;
use Kirby\Data\Json;
use Kirby\Toolkit\A;

class Middlewares
{
    /**
     * Checks if a bearer token was sent with the request and
     * if it matches the one configured in `.env`
     *
     * @return \KirbyHeadless\Api\ApiResponse|void
     */
    public static function hasBearerToken()
    {
        $authorization = kirby()->request()->header('Authorization');

        if (
            env('KIRBY_HEADLESS_API_TOKEN') &&
            (!$authorization || $authorization !== 'Bearer ' . env('KIRBY_HEADLESS_API_TOKEN'))
        ) {
            return ApiResponse::create(401);
        }
    }

    /**
     * Tries to parse the raw request body as JSON and
     * ends the request with 400 if that fails
     *
     * @param mixed $context
     * @return mixed
     * @throws Exception
     */
    public static function parseJson($context)
    {
        try {
            $raw = kirby()->request()->body()->contents();
            $json = Json::decode($raw);
            $context['json'] = $json;
            return $context;
        } catch (Exception $e) {
            return ApiResponse::create(400, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Checks for a data key in an already parsed JSON body and
     * ends the request with 400 if that fails
     *
     * @param array $context
     * @return \KirbyHeadless\Api\ApiResponse|void
     */
    public static function jsonBodyHasDataKey($context)
    {
        if (!isset($context['json']['data'])) {
            return ApiResponse::create(400, [
                'error' => '"data" key is missing',
            ]);
        }
    }

    /**
     * Checks if the parsed JSON contains a data key that is an
     * associative array and ends the request with 400 if that fails
     *
     * @param array $context
     * @return \KirbyHeadless\Api\ApiResponse|void
     */
    public static function dataIsAssociativeArray($context)
    {
        $data = $context['json']['data'];
        if (A::isAssociative($data)) {
            return ApiResponse::create(400, [
                'error' => '"data" key is not an array',
            ]);
        }
    }
}
