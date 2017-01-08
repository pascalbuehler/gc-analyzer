<?php
namespace Helper;

class CoordsHelper {
    public static function convertDecimalToDecimalMinute($lat, $lng) {
        $nord = 'N '.sprintf('%02d', (int)$lat).'°'.sprintf('%06.3f', round((($lat - (int)$lat) * 60), 3));
        $est = 'E '.sprintf('%03d', (int)$lng).'°'.sprintf('%06.3f', round((($lng - (int)$lng) * 60), 3));
        
        return $nord.' '.$est;
    }
}