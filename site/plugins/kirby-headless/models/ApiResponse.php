<?php

namespace KirbyHeadless\Api;

use Exception;
use Kirby\Data\Json;
use Kirby\Http\Response;

/**
 * Enforces consistent JSON responses by
 * wrapping Kirby's `Response` class
 */
class ApiResponse
{
    /**
     * Create an API response
     *
     * @param int $code
     * @param null|array $data
     * @return \Kirby\Http\Response
     */
    public static function create(int $code, ?array $data = null): Response
    {
        $body = self::createBody($code, $data);
        $json = Json::encode($body);
        return new Response($json, 'json', $code);
    }

    /**
     * Create the body for an api response
     *
     * @param int $code
     * @param null|array $data
     * @return array
     */
    private static function createBody(int $code, ?array $data): array
    {
        $base = [
            'code' => $code,
            'message' => self::getStatusMessage($code),
        ];

        if ($data !== null) {
            $base['data'] = $data;
        }

        return $base;
    }

    /**
     * Get the status message for the given code
     *
     * @param int $code
     * @return string
     * @throws Exception
     */
    private static function getStatusMessage(int $code): string
    {
        switch ($code) {
            case 100:
                return 'Continue';
            case 101:
                return 'Switching Protocols';
            case 200:
                return 'OK';
            case 201:
                return 'Created';
            case 202:
                return 'Accepted';
            case 203:
                return 'Non-Authoritative Information';
            case 204:
                return 'No Content';
            case 205:
                return 'Reset Content';
            case 206:
                return 'Partial Content';
            case 300:
                return 'Multiple Choices';
            case 301:
                return 'Moved Permanently';
            case 302:
                return 'Moved Temporarily';
            case 303:
                return 'See Other';
            case 304:
                return 'Not Modified';
            case 305:
                return 'Use Proxy';
            case 400:
                return 'Bad Request';
            case 401:
                return 'Unauthorized';
            case 402:
                return 'Payment Required';
            case 403:
                return 'Forbidden';
            case 404:
                return 'Not Found';
            case 405:
                return 'Method Not Allowed';
            case 406:
                return 'Not Acceptable';
            case 407:
                return 'Proxy Authentication Required';
            case 408:
                return 'Request Time-out';
            case 409:
                return 'Conflict';
            case 410:
                return 'Gone';
            case 411:
                return 'Length Required';
            case 412:
                return 'Precondition Failed';
            case 413:
                return 'Request Entity Too Large';
            case 414:
                return 'Request-URI Too Large';
            case 415:
                return 'Unsupported Media Type';
            case 500:
                return 'Internal Server Error';
            case 501:
                return 'Not Implemented';
            case 502:
                return 'Bad Gateway';
            case 503:
                return 'Service Unavailable';
            case 504:
                return 'Gateway Time-out';
            case 505:
                return 'HTTP Version not supported';
            default:
                throw new Exception('Unknown HTTP status code "' . htmlentities($code) . '"');
        }
    }
}
