<?php

namespace Core;

use Helper\ArrayHelper;

class Session {
    const SESSIONCLEANUP = 600; //seconds
    const TIMESTAMPS_KEY = 'timestamps';
    const BASEDATA_KEY = 'basedata';
    const IMAGEDATA_KEY = 'imagedata';
    const PLUGINDATA_KEY = 'plugindata';

    public static function init() {
        session_start();
        // Set timestamp for cleanup
        $runid = InputParameters::get('runid');
        $_SESSION[self::TIMESTAMPS_KEY][$runid] = time();
        // Do the cleanup
        self::cleanUp();
    }

    public static function set($name, $value) {
        $runid = InputParameters::get('runid');
        
        // Name as array
        if(is_array($name)) {
            $_SESSION[$runid] = ArrayHelper::insertNestedValue($_SESSION[$runid], $name, $value);
        }
        // Name as string
        else {
            $_SESSION[$runid][$name] = $value;
        }
        
        return true;
    }

    public static function get($name) {
        $runid = InputParameters::get('runid');

        // Name as array
        if(is_array($name)) {
            $value = ArrayHelper::getNestedValue($_SESSION[$runid], $name);
            if($value!==null) {
                return $value;
            }
        }
        // Name as string
        else {
            if(isset($_SESSION[$runid][$name])) {
                return $_SESSION[$runid][$name];
            }
        }
        
        return false;
    }
    
    public static function getAll() {
        $runid = InputParameters::get('runid');

        return $_SESSION[$runid];
    }
    
    private static function cleanUp() {
        $now = time();
        if(isset($_SESSION[self::TIMESTAMPS_KEY])) {
            foreach($_SESSION[self::TIMESTAMPS_KEY] as $runid=>$timestamp) {
                if($now>($timestamp+self::SESSIONCLEANUP)) {
                    unset($_SESSION[$runid]);
                    unset($_SESSION[self::TIMESTAMPS_KEY][$runid]);
                }
            }
        }
    }
}

