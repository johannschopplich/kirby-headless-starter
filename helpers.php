<?php

if (!function_exists('toJson')) {
    /**
     * Encode an array of data into a JSON string
     *
     * @param array $data
     * @return string|false
     */
    function toJson(array $data)
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
