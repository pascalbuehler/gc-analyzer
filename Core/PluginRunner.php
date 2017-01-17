<?php

namespace Core;

use Model\PluginResultModel;
use Plugin\AbstractPlugin;
use Plugin\PluginInterface;

class PluginRunner {
    const RUN_PLUGIN_SYNC = 'sync';
    const RUN_PLUGIN_ASYNC = 'async';
    const RUN_PLUGIN_NONE = 'none';
    
    public static function runPlugin($name, array $config, array &$data, array $runnedPlugins = []) {
        // Check plugin
        if(!isset($config['class'])) {
            return false;
        }
        if(isset($config['runbydefault']) && $config['runbydefault']===false) {
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
                if(!in_array($dependency, $runnedPlugins)) {
                    $plugin->setStatus(AbstractPlugin::PLUGIN_STATUS_FAILED);
                    break;
                }
            }
        }

        // Create plugin result
        $pluginResult = new PluginResultModel();
        $pluginResult->name = $name;
        $pluginResult->status = $plugin->getStatus();

        // Run plugin if possbile
        if($plugin->getStatus()==AbstractPlugin::PLUGIN_STATUS_OK) {
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
