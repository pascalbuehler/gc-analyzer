<?php

namespace Core;

use Helper\ConfigHelper;
use Layout\Layout;
use Model\PluginResultModel;
use Plugin\PluginInterface;

class PluginRunner {
    const RUN_PLUGIN_SYNC = 'sync';
    const RUN_PLUGIN_ASYNC = 'async';
    const RUN_PLUGIN_NONE = 'none';
    
    public static function runAllPlugins(Layout $layout, array &$data) {
        $config = ConfigHelper::getConfig();
        if(!isset($config['plugins']) || !is_array($config['plugins']) || !count($config['plugins'])>0) {
            throw new \Exception('No plugins to run');
        }

        $runnedPlugins = [];
        foreach($config['plugins'] as $pluginName => $pluginConfig) {
            $pluginResult = self::runPlugin($pluginName, $pluginConfig, $data, $runnedPlugins);
            if($pluginResult!==false) {
                Session::set([Session::PLUGINDATA_KEY, $pluginName], $pluginResult->result);
                $layout->addPluginData($pluginResult);
                $runnedPlugins[] = $pluginName;
            }
        }
    }
    
    public static function runSinglePlugin($pluginName, Layout $layout, array &$data) {
        $config = ConfigHelper::getConfig();
        if(!isset($config['plugins']) || !is_array($config['plugins']) || !in_array($pluginName, array_keys($config['plugins']))) {
            throw new \Exception('Plugins "'.$pluginName.'" not availabe');
        }

        $pluginConfig = $config['plugins'][$pluginName];
        $pluginResult = self::runPlugin($pluginName, $pluginConfig, $data, []);
        if($pluginResult!==false) {
            Session::set([Session::PLUGINDATA_KEY, $pluginName], $pluginResult->result);
            $layout->addPluginData($pluginResult);
        }
    }

    private static function runPlugin($name, array $config, array &$data, array $runnedPlugins = []) {
        // Check plugin
        if(!isset($config['class'])) {
            return false;
        }
        if(isset($config['runmode']) && $config['runmode']===self::RUN_PLUGIN_NONE) {
            return false;
        }

        // Load plugin
        $parameters = isset($config['parameters']) ? $config['parameters'] : [];
        $plugin = new $config['class']($data, $parameters);

        // Check plugin
        if(!in_array(PluginInterface::class, class_implements($plugin))) {
            return false;
        }
        
        // Check plugin dependencies
        if(isset($config['dependencies']) && is_array($config['dependencies']) && count($config['dependencies'])>0) {
            foreach($config['dependencies'] as $dependency) {
                $hasNotRunned = !in_array($dependency, $runnedPlugins);
                $hasNoData = Session::get([Session::PLUGINDATA_KEY, $dependency])===false;
                if($hasNotRunned && $hasNoData) {
                    $plugin->setStatus(PluginResultModel::PLUGIN_STATUS_FAILED);
                    break;
                }
            }
        }

        // Create plugin result
        $pluginResult = new PluginResultModel();
        $pluginResult->name = $name;
        $pluginResult->status = $plugin->getStatus();

        // Run plugin if possbile
        if($plugin->getStatus()==PluginResultModel::PLUGIN_STATUS_OK) {
            $timeStart = microtime(true);

            $plugin->calculate();
            $pluginResult->result = $plugin->getResult();
            $pluginResult->output = $plugin->getOutput();
            $pluginResult->success = $plugin->getSuccess();

            $timeEnd = microtime(true);
            $pluginResult->time = $timeEnd - $timeStart;
        }
        
        return $pluginResult;
    }
}
