<?php

namespace Services\Http;

class Util
{
    public static function parseCookieHeader($header)
    {
        $cookies = array();
        $header = rtrim($header, "\r\n");
        $headerPieces = preg_split('@\s*[;,]\s*@', $header);
        foreach ($headerPieces as $c) {
            $cParts = explode('=', $c, 2);
            if (count($cParts) === 2) {
                $key = urldecode($cParts[0]);
                $value = urldecode($cParts[1]);
                if (!isset($cookies[$key])) {
                    $cookies[$key] = $value;
                }
            }
        }

        return $cookies;
    }
}
