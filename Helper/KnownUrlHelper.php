<?php
namespace Helper;

class KnownUrlHelper {
    public static function isCheckerUrl($url) {
        $host = parse_url($url, PHP_URL_HOST);
        
        if ($host == 'geocheck.org' || $host == 'www.geocheck.org' || $host == 'geochecker.com' || $host == 'www.geochecker.com')
        {
            return true;
        }
        return false;
    }
    
    public static function isGroundspeakUrl($url) {
        $host = parse_url($url, PHP_URL_HOST);
        
        if ($host == 'geocaching.com' || $host == 'www.geocaching.com' || $host == 'coord.info')
        {
            return true;
        }
        return false;
    }
}