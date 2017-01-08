<?php
namespace Plugin\ListingBasics;

use Helper\ConfigHelper;
use Helper\CoordsHelper;
use Helper\WaypointHelper;

class ListingBasics extends \Plugin\AbstractPlugin {
    private $googleApiToken = false;
    
    public function calculate() {
        $config = ConfigHelper::getConfig();
        $this->googleApiToken = isset($config['googleApiToken']) ? $config['googleApiToken'] : false;
    }

    public function getResult() {
        return '';
    }

    public function getOutput() {

        $source = '';

        $source.= '<div class="row">'.PHP_EOL;
        $source.= '  <div class="col-lg-10 col-md-9 col-xs-8">'.PHP_EOL;
        $source.= '    <h3></span><a href="'.$this->data['Url'].'" target="_blank"><img src="'.$this->data['CacheType']['ImageURL'].'" alt="'.$this->data['CacheType']['GeocacheTypeName'].'" style="padding-right: 10px">'.$this->data['Name'].'</a></h3>'.PHP_EOL;
        $source.= '  </div>'.PHP_EOL;
        $source.= '  <div class="col-lg-2 col-md-3 col-xs-4 text-right">'.PHP_EOL;
        $source.= '    <h3>'.$this->data['Code'].'</h3>'.PHP_EOL;
        $source.= '  </div>'.PHP_EOL;
        $source.= '</div>'.PHP_EOL;
        $source.= '<div class="row">'.PHP_EOL;
        $source.= '  <div class="col-lg-6 col-md-6 col-xs-12">'.PHP_EOL;
        $source.= '    <p>Owner: '.$this->data['Owner']['UserName'].' (Hides: '.$this->data['Owner']['HideCount'].' | Finds: '.$this->data['Owner']['FindCount'].')</p>'.PHP_EOL;
        $source.= '    <p>PlacedBy: '.$this->data['PlacedBy'].'</p>'.PHP_EOL;
        $source.= '    <p>Size: <img src="https://www.geocaching.com/images/icons/container/'.str_replace(' ', '_', $this->data['ContainerType']['ContainerTypeName']).'.gif" /> '.$this->data['ContainerType']['ContainerTypeName'].'</p>'.PHP_EOL;
        $source.= '    <p>Difficulty: <img src="https://www.geocaching.com/images/stars/stars'.str_replace('.', '_', $this->data['Difficulty']).'.gif" /> / Terrain: <img src="https://www.geocaching.com/images/stars/stars'.str_replace('.', '_', $this->data['Terrain']).'.gif" /></p>'.PHP_EOL;
        $source.= '    <p>Hints:<br />'.str_replace("\n", '<br />', $this->data['EncodedHints']).'</p>'.PHP_EOL;
        $source.= '    <p>FavoritePoints: '.$this->data['FavoritePoints'].'</p>'.PHP_EOL;
        $source.= '    <p>Country / State: '.$this->data['Country'].' '.$this->data['State'].'</p>'.PHP_EOL;
        $source.= '    <p>Coords: '.$this->getCoordsDisplay($this->data['Latitude'], $this->data['Longitude']).'</p>'.PHP_EOL;

        if(isset($this->data['AdditionalWaypoints']) && is_array($this->data['AdditionalWaypoints']) && count($this->data['AdditionalWaypoints'])>0) {
            $source.= '    <h3>Waypoints</h3>'.PHP_EOL;
            foreach ($this->data['AdditionalWaypoints'] as $waypoint) {
                $source.= '    <h4>'.$waypoint['Name'].' '.$waypoint['Type'].' ('.$waypoint['Code'].')</h4>'.PHP_EOL;
                $source.= '    <div class="caption">'.PHP_EOL;
                $source.= '      <table class="table table-condensed">'.PHP_EOL;
                if(isset($waypoint['Description']) && strlen($waypoint['Description'])>0) {
                    $source.= '        <tr><td valign="top">Description</td><td>'.$waypoint['Description'].'</td></tr>'.PHP_EOL;
                }
                if(floatval($waypoint['Latitude'])>0 && floatval($waypoint['Longitude'])>0) {
                    $source.= '        <tr><td valign="top">Coords</td><td>'.$this->getCoordsDisplay($waypoint['Latitude'], $waypoint['Longitude']).'</td></tr>'.PHP_EOL;
                }
                $source.= '      </table>'.PHP_EOL;
                $source.= '    </div>'.PHP_EOL;
            }
        }

        $source.= '  </div>'.PHP_EOL;
        $source.= '  <div class="col-lg-6 col-md-6 col-xs-12">'.PHP_EOL;
        if($this->googleApiToken!==false) {
            $source.= '    <div id="map"></div>'.PHP_EOL;
            $source.= '    <script type="text/javascript">'.PHP_EOL;
            $source.= $this->getGoogleMapsJS();
            $source.= '    </script>'.PHP_EOL;
            $source.= '    <script async defer src="https://maps.googleapis.com/maps/api/js?key='.$this->googleApiToken.'&callback=initMap"></script>'.PHP_EOL;
        }
        else {
            $source.= '    googleApiToken not configured: search not possible'.PHP_EOL;
        }
        $source.= '  </div>'.PHP_EOL;
        $source.= '</div>'.PHP_EOL;

        return $source;
    }

    private function getCoordsDisplay($lat, $lng)
    {
        $ret = CoordsHelper::convertDecimalToDecimalMinute($lat, $lng);
        
        $ret .= ' <a href="http://www.openstreetmap.org/?mlat='.$lat.'&mlon=%20'.$lng.'&zoom=17&layers=M" target="_blank"><span class="glyphicon glyphicon-map-marker"></span>OSM</a>';
        $ret .= ' <a href="http://maps.google.com/maps?q=loc:'.$lat.','.$lng.'" target="_blank"><span class="glyphicon glyphicon-map-marker"></span>Google</a>';
        $ret .= ' <a href="https://tools.retorte.ch/map/?wgs84='.$lat.','.$lng.'&zoom=18" target="_blank"><span class="glyphicon glyphicon-map-marker"></span>Retorte</a>';

        return $ret;
    }
    
    private function getGoogleMapsJS() {
        $js = '';
        
        $waypoints = WaypointHelper::getWaypoints($this->data);
        $bounds = $this->getGoogleMapsBounds($waypoints);
        
        $js.= 'var map;'.PHP_EOL;
        $js.= 'var infoWindow'.PHP_EOL;
        $js.= 'function initMap() {'.PHP_EOL;

        // Init Map
        $js.= '    var mapBounds = new google.maps.LatLngBounds(new google.maps.LatLng('.$bounds['LatitudeMin'].', '.$bounds['LongitudeMin'].'), new google.maps.LatLng('.$bounds['LatitudeMax'].', '.$bounds['LongitudeMax'].'));'.PHP_EOL;
        $js.= '    map = new google.maps.Map(document.getElementById("map"), {'.PHP_EOL;
        $js.= '        center: mapBounds.getCenter(),'.PHP_EOL;
        $js.= '        zoom: 12'.PHP_EOL;
        $js.= '    });'.PHP_EOL;
        $js.= '    infoWindow = new google.maps.InfoWindow();'.PHP_EOL;
        $js.= '    map.fitBounds(mapBounds);'.PHP_EOL;

        // Marker for cache itself
        $js.= $this->getGoogleMapsMarkerJS(
            $waypoints[0]->id,
            $waypoints[0]->latitude, 
            $waypoints[0]->longitude, 
            str_replace('\'', '\\\'', $waypoints[0]->title).'\\n'.$waypoints[0]->coordsDisplay,
            str_replace('\'', '\\\'', $waypoints[0]->description),
            $waypoints[0]->icon
        );
        // 3k radius around center for mistery
        if($this->data['CacheType']['GeocacheTypeId']==8) {
            $js.= '    var circle'.$waypoints[0]->id.' = new google.maps.Circle({'.PHP_EOL;
            $js.= '        strokeColor: \'red\','.PHP_EOL;
            $js.= '        strokeOpacity: 1.0,'.PHP_EOL;
            $js.= '        strokeWeight: 2,'.PHP_EOL;
            $js.= '        fillColor: \'red\','.PHP_EOL;
            $js.= '        fillOpacity: 0.02,'.PHP_EOL;
            $js.= '        map: map,'.PHP_EOL;
            $js.= '        clickable: false,'.PHP_EOL;
            $js.= '        center: new google.maps.LatLng('.$waypoints[0]->latitude.', '.$waypoints[0]->longitude.'),'.PHP_EOL;
            $js.= '        radius: 3000'.PHP_EOL;
            $js.= '    });'.PHP_EOL;
        }
        array_shift($waypoints);

        // Markers for additional waypoints
        if(count($waypoints)>0) {
            foreach($waypoints as $waypoint) {
                if(floatval($waypoint->latitude)>0 && floatval($waypoint->longitude)>0) {
                    $js.= $this->getGoogleMapsMarkerJS(
                        $waypoint->id,
                        $waypoint->latitude, 
                        $waypoint->longitude, 
                        str_replace('\'', '\\\'', $waypoint->title).' ('.$waypoint->coordsDisplay.')',
                        str_replace('\'', '\\\'', $waypoint->description),
                        $waypoint->icon
                    );
                }
            }
        }
        
        
        $js.= '};'.PHP_EOL;
        
        return $js;
    }
    
    
    private function getGoogleMapsBounds(array $waypoints) {
        $bounds = array(
            'LatitudeMin' => 90,
            'LatitudeMax' => -90,
            'LongitudeMin' => 180,
            'LongitudeMax' => -180,
        );

        foreach($waypoints as $waypoint) {
            if(floatval($waypoint->latitude)>0) {
                $bounds['LatitudeMin'] = $waypoint->latitude<$bounds['LatitudeMin'] ? $waypoint->latitude : $bounds['LatitudeMin'];
                $bounds['LatitudeMax'] = $waypoint->latitude>$bounds['LatitudeMax'] ? $waypoint->latitude : $bounds['LatitudeMax'];
            }
            if(floatval($waypoint->longitude)>0) {
                $bounds['LongitudeMin'] = $waypoint->longitude<$bounds['LongitudeMin'] ? $waypoint->longitude : $bounds['LongitudeMin'];
                $bounds['LongitudeMax'] = $waypoint->longitude>$bounds['LongitudeMax'] ? $waypoint->longitude : $bounds['LongitudeMax'];
            }
        }

        if($bounds['LatitudeMin']==$bounds['LatitudeMax']) {
            $bounds['LatitudeMin']-= 0.01;
            $bounds['LatitudeMax']+= 0.01;
        }
        if($bounds['LongitudeMin']==$bounds['LongitudeMax']) {
            $bounds['LongitudeMin'] -= 0.01;
            $bounds['LongitudeMax'] += 0.01;
        }
        
        return $bounds;
    }
    
    private function getGoogleMapsMarkerJS($id, $lat, $lng, $title, $description, $icon) {
        $js = '';
        $js.= '    var marker'.$id.' = new google.maps.Marker({'.PHP_EOL;
        $js.= '        position: new google.maps.LatLng('.$lat.', '.$lng.'),'.PHP_EOL;
        $js.= '        map: map,'.PHP_EOL;
        if($icon) {
            $js.= '        icon: \''.$icon.'\','.PHP_EOL;
        }
        $js.= '        title: \''.$title.'\''.PHP_EOL;
        $js.= '    });'.PHP_EOL;
        if(strlen($description)>0) {
            $description = str_replace(array("\r", "\n"), '', nl2br($description));
            $js.= '    google.maps.event.addListener(marker'.$id.', \'click\', function(ev) {'.PHP_EOL;
            $js.= '        infoWindow.setContent(\'<b>'.$title.'</b><br />'.$description.'\');'.PHP_EOL;
            $js.= '        infoWindow.open(map, marker'.$id.');'.PHP_EOL;
            $js.= '    });'.PHP_EOL;
        }
        return $js;
    }
}
