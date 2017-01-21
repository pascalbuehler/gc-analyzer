<?php
use Core\InputParameters;
use Core\PluginRunner;
use Core\Router;
use Helper\ApiHelper;
use Helper\ConfigHelper;
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
$env = getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'dist';

// INPUT PARAMETERS
InputParameters::init();

// ROUTE
$rewrite = filter_input(INPUT_GET, 'rewrite', FILTER_SANITIZE_STRING);
if($rewrite) { 
    $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_STRING);
    Router::route($url);
}

// CONFIG
ConfigHelper::init('Config/'.$env.'.php');
$config = ConfigHelper::getConfig();

// RUN
switch(InputParameters::getParameter('page')) {
    case Router::PAGE_HOME;
        $layout = new Layout('home', ['config' => $config]);
        $layout->render();
        break;
    case Router::PAGE_ANALYZE;
        // BASEDATA
        $data = ApiHelper::getBaseData();

        // ENRICH BASEDATA
        // @todo

        // LAYOUT INIT
        $layout = new Layout('analyze', ['layoutConfig' => $config['layout']]);

        // PLUGINS
        if(isset($config['plugins']) && is_array($config['plugins']) && count($config['plugins'])>0) {
            $runnedPlugins = [];
            foreach($config['plugins'] as $pluginName => $pluginConfig) {
                $pluginResult = PluginRunner::runPlugin($pluginName, $pluginConfig, $data, $runnedPlugins);
                if($pluginResult!==false) {
                    $data['plugins'][$pluginName] = $pluginResult->result;
                    $layout->addPluginData($pluginResult);
                    $runnedPlugins[] = $pluginName;
                }
            }
        }
        // LAYOUT RENDER
        $layout->render();
        break;
}