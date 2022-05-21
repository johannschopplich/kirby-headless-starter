<?php

namespace KirbyHeadless\Api;

use Exception;
use KirbyHeadless\Api\Api;
use Kirby\Data\Json;

class Middlewares
{
    /**
     * Checks if a bearer token was sent with the request and
     * if it matches the one configured in `.env`
     *
     * @return \Kirby\Http\Response|void
     */
    public static function hasBearerToken()
    {
        $authorization = kirby()->request()->header('Authorization');
        $token = env('KIRBY_HEADLESS_API_TOKEN');

        if (
            !empty($token) &&
            (empty($authorization) || $authorization !== 'Bearer ' . $token)
        ) {
            return Api::createResponse(401);
        }
    }

    /**
     * Tries to parse the raw request body as JSON and
     * ends the request with 400 if that fails
     *
     * @param array $context
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
            return Api::createResponse(400, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Checks for a data key in an already parsed JSON body and
     * ends the request with 400 if that fails
     *
     * @param array $context
     * @return \Kirby\Http\Response|void
     */
    public static function jsonBodyHasDataKey($context)
    {
        if (!isset($context['json']['data'])) {
            return Api::createResponse(400, [
                'error' => '"data" key is missing',
            ]);
        }
    }
}
