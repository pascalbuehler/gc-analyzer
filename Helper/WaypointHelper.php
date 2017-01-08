<?php
namespace Helper;

use Model\WaypointModel;
use Helper\CoordsHelper;

class WaypointHelper {
    private static $waypoints = false;
    
    public static function getWaypoints($data) {
        if(self::$waypoints===false) {
            self::$waypoints = self::collectWaypoints($data);
        }

        return self::$waypoints;
    }

    private static function collectWaypoints($data) {
        // Cache itself
        $cacheWaypoint = new WaypointModel();
        $cacheWaypoint->id = $data['Code'];
        $cacheWaypoint->title = $data['Name'].' ('.$data['Code'].')';
        $cacheWaypoint->description = '';
        $cacheWaypoint->icon = $data['CacheType']['ImageURL'];
        $cacheWaypoint->latitude = $data['Latitude'];
        $cacheWaypoint->longitude = $data['Longitude'];
        $cacheWaypoint->coordsDisplay = CoordsHelper::convertDecimalToDecimalMinute($data['Latitude'], $data['Longitude']);
        self::$waypoints[] = $cacheWaypoint;
        
        // Additional waypoints
        if(isset($data['AdditionalWaypoints']) && is_array($data['AdditionalWaypoints']) && count($data['AdditionalWaypoints']) > 0) {
            foreach($data['AdditionalWaypoints'] as $waypoint) {
                $additionalWaypoint = new WaypointModel();
                $additionalWaypoint->id = $waypoint['Code'];
                $additionalWaypoint->title = $waypoint['Name'];
                $additionalWaypoint->description = $waypoint['Description'].(strlen($waypoint['Comment'])>0 ? PHP_EOL.$waypoint['Comment'] : '');
                $additionalWaypoint->icon = self::getWaypointIcon16FromType($waypoint['Type']);
                $additionalWaypoint->latitude = $waypoint['Latitude'];
                $additionalWaypoint->longitude = $waypoint['Longitude'];
                $additionalWaypoint->coordsDisplay = CoordsHelper::convertDecimalToDecimalMinute($waypoint['Latitude'], $waypoint['Longitude']);
                self::$waypoints[] = $additionalWaypoint;
            }
        }
        
        return self::$waypoints;
    }
    
    private static function getWaypointIcon16FromType($waypointType) {
        $icon32 = '';
        switch($waypointType) {
            case 'Waypoint|Final Location':
                $icon32 = 'http://www.geocaching.com/images/wpttypes/sm/flag.jpg';
                break;
            case 'Waypoint|Parking Area':
                $icon32 = 'http://www.geocaching.com/images/wpttypes/sm/pkg.jpg';
                break;
            case 'Waypoint|Physical Stage':
                $icon32 = 'http://www.geocaching.com/images/wpttypes/sm/stage.jpg';
                break;
            case 'Waypoint|Reference Point':
                $icon32 = 'http://www.geocaching.com/images/wpttypes/sm/waypoint.jpg';
                break;
            case 'Waypoint|Trailhead':
                $icon32 = 'http://www.geocaching.com/images/wpttypes/sm/trailhead.jpg';
                break;
            case 'Waypoint|Virtual Stage':
                $icon32 = 'http://www.geocaching.com/images/wpttypes/sm/puzzle.jpg';
                break;
        }
        return $icon32;
    }
}