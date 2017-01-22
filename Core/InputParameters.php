<?php

namespace Core;

class InputParameters {
    private static $parameters = [];
    
    public static function init() {
        // Defaults
        InputParameters::set('page', Router::PAGE_HOME);
        InputParameters::set('runid', uniqid('gcanalyzer'));

        // GET Parameters
        foreach($_GET as $name => $value) {
            if(in_array($name, ['url', 'rewrite'])) {
                continue;
            }
            InputParameters::set($name, $value);
        }
    }
    
    public static function set($name, $value) {
        self::$parameters[$name] = $value;
        return true;
    }

    public static function get($name) {
        if(isset(self::$parameters[$name])) {
            return self::$parameters[$name];
        }
        
        return false;
    }

    public static function getAll() {
        return self::$parameters;
    }
}