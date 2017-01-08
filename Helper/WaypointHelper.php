<?php
namespace Helper;

use Model\WaypointModel;
use Helper\CoordsHelper;

class WaypointHelper {
    private static $waypoints = false;
    
    public static function getAllWaypoints($data) {
        if(self::$waypoints===false) {
            self::$waypoints = self::collectWaypoints($data);
        }

        return self::$waypoints;
    }

    public static function getAdditionalWaypoints($data) {
        if(self::$waypoints===false) {
            self::$waypoints = self::collectWaypoints($data);
        }

        return array_slice(self::$waypoints, 1);
    }

    private static function collectWaypoints($data) {
        // Cache itself
        $cacheWaypoint = new WaypointModel();
        $cacheWaypoint->id = $data['Code'];
        $cacheWaypoint->title = $data['Name'].' ('.$data['Code'].')';
        $cacheWaypoint->description = '';
        $cacheWaypoint->type = $data['CacheType']['GeocacheTypeName'];
        $cacheWaypoint->icon = self::getNewIcon($data['CacheType']['ImageURL']);
        $cacheWaypoint->latitude = $data['Latitude'];
        $cacheWaypoint->longitude = $data['Longitude'];
        $cacheWaypoint->coordsDisplay = CoordsHelper::convertDecimalToDecimalMinute($data['Latitude'], $data['Longitude']);
        
        // Additional waypoints
        $additionalWaypoints = [];
        if(isset($data['AdditionalWaypoints']) && is_array($data['AdditionalWaypoints']) && count($data['AdditionalWaypoints']) > 0) {
            foreach($data['AdditionalWaypoints'] as $waypoint) {
                // Add description to
                $additionalWaypoint = new WaypointModel();
                $additionalWaypoint->id = $waypoint['Code'];
                $additionalWaypoint->title = $waypoint['Name'];
                $additionalWaypoint->description = $waypoint['Description'].(strlen($waypoint['Comment'])>0 ? PHP_EOL.$waypoint['Comment'] : '');
                $additionalWaypoint->type = $waypoint['Type'];
                $additionalWaypoint->icon = self::getWaypointIconFromType($waypoint['Type']);
                $additionalWaypoint->latitude = $waypoint['Latitude'];
                $additionalWaypoint->longitude = $waypoint['Longitude'];
                $additionalWaypoint->coordsDisplay = CoordsHelper::convertDecimalToDecimalMinute($waypoint['Latitude'], $waypoint['Longitude']);
                $additionalWaypoints[] = $additionalWaypoint;
            }
        }
        
        self::$waypoints = array_merge(array($cacheWaypoint), $additionalWaypoints);
        
        return self::$waypoints;
    }
    
    private static function getWaypointIconFromType($waypointType) {
        $icon = null;
        switch($waypointType) {
            case 'Waypoint|Final Location':
                $icon = 'http://www.geocaching.com/images/wpttypes/sm/flag.jpg';
                break;
            case 'Waypoint|Parking Area':
                $icon = 'http://www.geocaching.com/images/wpttypes/sm/pkg.jpg';
                break;
            case 'Waypoint|Physical Stage':
                $icon = 'http://www.geocaching.com/images/wpttypes/sm/stage.jpg';
                break;
            case 'Waypoint|Reference Point':
                $icon = 'http://www.geocaching.com/images/wpttypes/sm/waypoint.jpg';
                break;
            case 'Waypoint|Trailhead':
                $icon = 'http://www.geocaching.com/images/wpttypes/sm/trailhead.jpg';
                break;
            case 'Waypoint|Virtual Stage':
                $icon = 'http://www.geocaching.com/images/wpttypes/sm/puzzle.jpg';
                break;
        }
        return $icon;
    }
    
    private static function getNewIcon($imageUrl) {
        $match = [];
        if(preg_match('&.*/([0-9]+)\.gif&', $imageUrl, $match)) {
            $imageUrl = 'https://www.geocaching.com/map/images/mapicons/'.$match[1].'.png';
        }
        return $imageUrl;
    }
}