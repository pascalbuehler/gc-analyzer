<?php

namespace Core;

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
            $_SESSION[$runid] = self::insertNestedValue($_SESSION[$runid], $name, $value);
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
            $value = self::getNestedValue($_SESSION[$runid], $name);
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
    
    private static function insertNestedValue($array, $keys, $value) {
        $a = &$array;
        while(count($keys)>0) {
            $k = array_shift($keys);
            if(!is_array($a)) {
                $a = array();
            }
            $a = &$a[$k];
        }
        $a = $value;

        return $array;
    }

    private static function getNestedValue($array, $keys) {
        $found = true;
        $a = &$array;
        while(count($keys)>0) {
            $k = array_shift($keys);
            if(!isset($a[$k])) {
                $found = false;
                break;
            }
            $a = $a[$k];
        }

        return $found ? $a : null;
    }
    
}

