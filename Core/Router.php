<?php

namespace Core;

use Core\InputParameters;

class Router {
    const PAGE_HOME = 'home';
    const PAGE_ANALYZE = 'analyze';
    
    public static function route($url) {
        $url = mb_substr($url, -1, 1)=='/' ? mb_substr($url, 0, -1) : $url;
        $urlpieces = array_map('strtolower', explode('/', $url));

        // Home
        if(!is_array($urlpieces) || count($urlpieces)<1) {
            InputParameters::setParameter('page', self::PAGE_HOME);
            return true;
        }
        
        // Analyze
        if(count($urlpieces)==1 && isset($urlpieces[0]) && substr($urlpieces[0], 0, 2)=='gc') {
            InputParameters::setParameter('code', $urlpieces[0]);
            InputParameters::setParameter('page', self::PAGE_ANALYZE);
            return true;
        }
    
        return false;
    }
}
