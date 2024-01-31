<?php

namespace Antikirra;

/**
 * @param string $string
 * @return string
 */
function base64url_encode($string)
{
    return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
}

/**
 * @param string $string
 * @return string|false
 */
function base64url_decode($string)
{
    return base64_decode(strtr($string, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($string)) % 4));
}
