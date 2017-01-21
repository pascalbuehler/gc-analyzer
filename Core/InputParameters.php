<?php

namespace Core;

class InputParameters {
    private static $parameters = [];
    
    public static function init() {
        // GET Parameters
        foreach($_GET as $name => $value) {
            if(in_array($name, ['url', 'rewrite'])) {
                continue;
            }
            InputParameters::setParameter($name, $value);
        }
        
        // RunID
        InputParameters::setParameter('runid', uniqid());
        
        // Defaults
        InputParameters::setParameter('page', Router::PAGE_HOME);
    }
    
    public static function setParameter($name, $value) {
        self::$parameters[$name] = $value;
    }

    public static function getParameter($name) {
        if(isset(self::$parameters[$name])) {
            return self::$parameters[$name];
        }
        
        return false;
    }

    public static function getParameters() {
        return self::$parameters;
    }
}