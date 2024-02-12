<?php

namespace Antikirra;


if (!function_exists('Antikirra\\base64url_encode')) {
    /**
     * @param string $string
     * @return string
     */
    function base64url_encode($string)
    {
        return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
    }
}

if (!function_exists('Antikirra\\base64url_decode')) {
    /**
     * @param string $string
     * @return string|false
     */
    function base64url_decode($string)
    {
        return base64_decode(strtr($string, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($string)) % 4));
    }
}
