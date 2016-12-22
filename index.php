<?php
use Layout\Layout;
use Plugin\PluginInterface;

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

// MODE
$code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
if($code) {
    $mode = 'analyze';
}
else {
    $mode = 'home';
}

// RUN
switch($mode) {
    case 'home';
        $layout = new Layout('home', ['config' => $config]);
        $layout->render();
        break;
    case 'analyze';
        // BASEDATA
        $url = $config['apiEndpoint'].'?'.http_build_query($config['apiParameters']);
        $data = file_get_contents($url);
        if(!$data) {
            die('Api not reachable ('.$url.')');
        }
        $data = json_decode($data, true);

        // ENRICH BASEDATA
        // @todo

        // LAYOUT INIT
        $layout = new Layout('analyze');

        // PLUGINS
        if(isset($config['plugins']) && is_array($config['plugins']) && count($config['plugins'])>0) {
            $runnedPlugins = [];
            foreach($config['plugins'] as $pluginName => $pluginConfig) {
                // Load plugin
                if(!isset($pluginConfig['class'])) {
                    continue;
                }
                $parameters = isset($pluginConfig['parameters']) ? $pluginConfig['parameters'] : [];
                $plugin = new $pluginConfig['class']($data, $parameters);
                // Check plugin
                if(!in_array(PluginInterface::class, class_implements($plugin))) {
                    continue;
                }
                // Check plugin dependencies
                if(isset($pluginConfig['dependencies']) && is_array($pluginConfig['dependencies']) && count($pluginConfig['dependencies'])>0) {
                    foreach($pluginConfig['dependencies'] as $dependency) {
                        if(!in_array($dependency, $runnedPlugins)) {
                            $plugin->setStatus(Plugin\AbstractPlugin::PLUGIN_STATUS_FAILED);
                            continue;
                        }
                    }
                }
                // Run plugin
                if($plugin->getStatus()==Plugin\AbstractPlugin::PLUGIN_STATUS_OK) {
                    $plugin->calculate();
                    $data['plugins'][$pluginName] = $plugin->getResult();
                    $pluginOutput = $plugin->getOutput();
                }
                else {
                    $pluginOutput = '';
                }
                $layout->addPluginData($pluginName, $pluginOutput, $plugin->getStatus(), $plugin->getSuccess());
                $runnedPlugins[] = $pluginName;
            }
        }
        // LAYOUT RENDER
        $layout->render();
        break;
}
