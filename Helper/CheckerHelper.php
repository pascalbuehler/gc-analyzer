<?php
namespace Helper;

class CheckerHelper {
    public static function isCheckerUrl($url) {
        if (strpos($url, 'geocheck.org') > -1 || strpos($url, 'geochecker.com') > -1)
        {
            return true;
        }
        return false;
    }
}