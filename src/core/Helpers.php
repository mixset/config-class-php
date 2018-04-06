<?php

namespace Core;

class Helpers
{
    /**
     * Get user's IP based on $_SERVER variable
     *
     * @param bool $ip2long
     * @return int
    */
    public static function getIP($ip2long = false)
    {
        if ($_SERVER['HTTP_CLIENT_IP']) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }

        if ($ip2long) {
            $ip = ip2long($ip);
        }

        return $ip;
    }

    /***
     * Simple helper for retrieving date in given format
     *
     * @param string $format
     * @return false|string
    */
    public static function getDate($format = 'd-m-Y, H:i:s')
    {
        return date($format);
    }
}
