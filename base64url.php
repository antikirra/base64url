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
    // Validate that input contains only valid base64url characters
    if (!preg_match('/\A[A-Za-z0-9_-]*\z/', $string)) {
        return false;
    }

    return base64_decode(strtr($string, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($string)) % 4));
}
