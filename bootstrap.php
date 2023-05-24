<?php

if (!function_exists('base64url_decode')) {
    /**
     * @param string $data
     * @return string
     */
    function base64url_decode($data)
    {
        return \Antikirra\Base64Url\Base64Url::decode($data);
    }
}

if (!function_exists('base64url_encode')) {
    /**
     * @param string $data
     * @return string
     */
    function base64url_encode($data)
    {
        return \Antikirra\Base64Url\Base64Url::encode($data);
    }
}
