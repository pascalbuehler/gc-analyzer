<?php
use Layout\Layout;

// SECURITY
// Unset PHP_SELF because it allows SQL-Injection and Cross-Site-Scripting
unset($PHP_SELF);
unset($_SERVER['PHP_SELF']);

// ERROR REPORTING
error_reporting(E_ALL);
ini_set('dislay_errors', 1);

// TIMEZONE
date_default_timezone_set('UTC');

// AUTOLOAD
spl_autoload_register(function($class) {
    $parts = explode('\\', $class);
    $classFilename = implode('/', $parts).'.php';
    if(file_exists($classFilename)) {
        require $classFilename;
    }
});

// ENVIRONMENT
$env = getenv('APPLICATION_ENV') ?: 'dist';

// CONFIG
$configfile = 'Config/'.$env.'.php';
$config = file_exists($configfile) ? include($configfile) : false;
if(!$config) {
    die('Config file not found (APPLICATION_ENV='.$env.')');
}

// BASEDATA
$url = $config['apiEndpoint'].'?'.http_build_query($config['apiParameters']);
$data = file_get_contents($url);
if(!$data) {
    die('Api not reachable ('.$url.')');
}
$data = json_decode($data);

// ENRICH BASEDATA
// @todo

// LAYOUT INIT
$layout = new Layout($data);

// PLUGINS
if(isset($config['plugins']) && is_array($config['plugins']) && count($config['plugins'])>0) {
    foreach($config['plugins'] as $pluginName => $pluginConfig) {
        if(!isset($pluginConfig['class'])) {
            continue;
        }
        $parameters = isset($pluginConfig['parameters']) ? $pluginConfig['parameters'] : [];
        $plugin = new $pluginConfig['class']($data, $parameters);
        $plugin->calculate();
        $data->plugins[$pluginName] = $plugin->getResult();
        $pluginOutput = $plugin->getOutput();
        if($pluginOutput!==false) {
            $layout->addPluginData($pluginName, $pluginOutput, $plugin->getSuccess());
        }
    }
}

// LAYOUT RENDER
$layout->render();