<?php

namespace Helper;

use Core\InputParameters;
use Core\Session;
use Helper\ConfigHelper;

class ApiHelper {
    public static function getBaseData() {
        $data = Session::get(Session::BASEDATA_KEY);
        if($data===false) {
            $config = ConfigHelper::getConfig();
            $config['apiParameters']['code'] = InputParameters::get('code');
            $url = $config['apiEndpoint'].'?'.http_build_query($config['apiParameters']);
            $data = self::callApi($url);
            Session::set(Session::BASEDATA_KEY, $data);
        }
        return $data;
    }
    
    public static function getImageData() {
        $data = Session::get(Session::IMAGEDATA_KEY);
        if($data===false) {
            $config = ConfigHelper::getConfig();
            $config['apiParameters']['code'] = InputParameters::get('code');
            $url = $config['apiEndpointImages'].'?'.http_build_query($config['apiParametersImages']);
            $data = self::callApi($url);
            Session::set(Session::IMAGEDATA_KEY, $data);
        }
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

