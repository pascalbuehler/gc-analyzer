<?php

namespace Core;

use Core\InputParameters;
use Helper\ConfigHelper;

class Router {
    const PAGE_HOME = 'home';
    const PAGE_ANALYZE = 'analyze';
    const PAGE_PLUGIN = 'plugin';
    
    public static function route($url) {
        $url = mb_substr($url, -1, 1)=='/' ? mb_substr($url, 0, -1) : $url;
        $urlpieces = strlen($url)>0 ? explode('/', $url) : [];

        // Home
        if(!is_array($urlpieces) || count($urlpieces)<1) {
            InputParameters::setParameter('page', self::PAGE_HOME);
            return true;
        }
        
        // Analyze
        if(count($urlpieces)==1 && isset($urlpieces[0]) && strtolower(substr($urlpieces[0], 0, 2))=='gc') {
            InputParameters::setParameter('code', $urlpieces[0]);
            InputParameters::setParameter('page', self::PAGE_ANALYZE);
            return true;
        }
        
        // Plugins
        if(count($urlpieces)==2) {
            $config = ConfigHelper::getConfig();
            if(in_array($urlpieces[1], array_keys($config['plugins']))) {
                InputParameters::setParameter('code', $urlpieces[0]);
                InputParameters::setParameter('plugin', $urlpieces[1]);
                InputParameters::setParameter('page', self::PAGE_PLUGIN);
                return true;
            }
        }
    
        return false;
    }
}
