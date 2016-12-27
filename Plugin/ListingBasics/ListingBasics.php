<?php
namespace Plugin\ListingBasics;

class ListingBasics extends \Plugin\AbstractPlugin {

    public function calculate() {

    }

    public function getResult() {
        return '';
    }

    public function getOutput() {

        $source = '';
//var_dump($this->data);
        $source .= '<div class="row">'.PHP_EOL;
        $source .= '  <div class="col-lg-10 col-md-9 col-xs-8">'.PHP_EOL;
        $source .= '    <h3><a href="'.$this->data['Url'].'" target="_blank"><img src="'.$this->data['CacheType']['ImageURL'].'" alt="'.$this->data['CacheType']['GeocacheTypeName'].'" style="padding-right: 10px">'.$this->data['Name'].'</a></h3>'.PHP_EOL;
        $source .= '  </div>'.PHP_EOL;
        $source .= '  <div class="col-lg-2 col-md-3 col-xs-4 text-right">'.PHP_EOL;
        $source .= '    <h3>'.$this->data['Code'].'</h3>'.PHP_EOL;
        $source .= '  </div>'.PHP_EOL;
        $source .= '</div>'.PHP_EOL;
        $source .= '<p>Owner: '.$this->data['Owner']['UserName'].' (Hides: '.$this->data['Owner']['HideCount'].' / Finds: '.$this->data['Owner']['FindCount'].')</p>'.PHP_EOL;
        $source .= '<p>PlacedBy: '.$this->data['PlacedBy'].'</p>'.PHP_EOL;
        $source .= '<p>Size: <img src="https://www.geocaching.com/images/icons/container/'.$this->data['ContainerType']['ContainerTypeName'].'.gif" /> '.$this->data['ContainerType']['ContainerTypeName'].'</p>'.PHP_EOL;
        $source .= '<p>Terrain: <img src="https://www.geocaching.com/images/stars/stars'.str_replace('.', '_', $this->data['Terrain']).'.gif" /> / Difficulty: <img src="https://www.geocaching.com/images/stars/stars'.str_replace('.', '_', $this->data['Difficulty']).'.gif" /></p>'.PHP_EOL;
        $source .= '<p>Hints:<br />'.str_replace("\n", '<br />', $this->data['EncodedHints']).'</p>'.PHP_EOL;
        $source .= '<p>FavoritePoints: '.$this->data['FavoritePoints'].'</p>'.PHP_EOL;
        $source .= '<p>Country / State: '.$this->data['Country'].' '.$this->data['State'].'</p>'.PHP_EOL;
        $source .= '<p>Coords: '.$this->convertToReadableCoords($this->data['Latitude'], $this->data['Longitude']).'</p>'.PHP_EOL;

        if (isset($this->data['AdditionalWaypoints']) && count($this->data['AdditionalWaypoints']) > 0)
        {
            $source.= '<h3>Waypoints</h3>'.PHP_EOL;

            foreach ($this->data['AdditionalWaypoints'] as $waypoint)
            {
                $source.= '<div class="row">'.PHP_EOL;
                $source.= '  <div class="col-lg-6">'.PHP_EOL;
                $source.= '    <h4>'.$waypoint['Name'].' '.$waypoint['Type'].' ('.$waypoint['Code'].')</h4>'.PHP_EOL;
                $source.= '    <div class="caption">'.PHP_EOL;
                $source.= '      <table class="table table-condensed">'.PHP_EOL;
                if (isset($waypoint['Description']) && strlen($waypoint['Description'])>0) {
                    $source.= '        <tr><td valign="top">Description</td><td>'.$waypoint['Description'].'</td></tr>'.PHP_EOL;
                }
                if (isset($waypoint['Latitude']) && isset($waypoint['Longitude'])) {
                    $source.= '        <tr><td valign="top">Coords</td><td>'.$this->convertToReadableCoords($waypoint['Latitude'], $waypoint['Longitude']).'</td></tr>'.PHP_EOL;
                }
                $source.= '      </table>'.PHP_EOL;
                $source.= '    </div>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;
                $source .= '</div>'.PHP_EOL;
            }
        }
        return $source;
    }

    private function convertToReadableCoords($lat, $long)
    {
        $ret = 'N '.sprintf('%02d', (int)$lat).'°'.sprintf('%06.3f', round((($lat - (int)$lat) * 60), 3)).' '.'E '.sprintf('%03d', (int)$long).'°'.sprintf('%06.3f', round((($long - (int)$long) * 60), 3));

        $ret .= ' <a href="http://www.openstreetmap.org/?mlat='.$lat.'&mlon=%20'.$long.'&zoom=17&layers=M" target="_blank">OSM</a>';
        $ret .= ' <a href="http://maps.google.com/maps?q=loc:'.$lat.','.$long.'" target="_blank">Google</a>';
        $ret .= ' <a href="https://tools.retorte.ch/map/?wgs84='.$lat.','.$long.'&zoom=18" target="_blank">Retorte</a>';

        return $ret;
    }
}
