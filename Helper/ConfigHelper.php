<?php
namespace Helper;

class ConfigHelper {
    private static $configFile = false;
    private static $config = false;
    
    public static function init($configFile) {
        self::$configFile = $configFile;
    }
    
    public static function getConfig() {
        if(self::$configFile===false) {
            die('No config file found.');
        }
        if(self::$config===false) {
            self::$config = file_exists(self::$configFile) ? include(self::$configFile) : false;
            if(!self::$config) {
                die('Config file not readable.');
            }
        }
        return self::$config;
    }
}