<?php

namespace Helper;

use Core\InputParameters;
use Helper\ConfigHelper;

class ApiHelper {
    public static function getBaseData() {
        $config = ConfigHelper::getConfig();
        $config['apiParameters']['code'] = InputParameters::getParameter('code');
        $url = $config['apiEndpoint'].'?'.http_build_query($config['apiParameters']);
        $data = self::callApi($url);
        return $data;
    }
    
    public static function getImageData() {
        $config = ConfigHelper::getConfig();
        $config['apiParameters']['code'] = InputParameters::getParameter('code');
        $url = $config['apiEndpointImages'].'?'.http_build_query($config['apiParametersImages']);
        $data = self::callApi($url);
        return $data;
    }
    
    private static function callApi($url) {
        $data = file_get_contents($url);
        if(!$data) {
            throw new \Exception('Api not reachable ('.$url.')');
        }
        elseif($data=='null') {
            throw new \Exception('Api returned nothing ('.$url.')');
        }

        $data = json_decode($data, true);
        return $data;
    }
}

