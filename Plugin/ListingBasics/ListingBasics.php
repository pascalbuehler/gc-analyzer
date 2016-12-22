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

        //$source .= print_r($this->data,true);

        $source .= '<div class="row">'.PHP_EOL;
        $source .= '  <div class="col-lg-10 col-md-9 col-xs-8">'.PHP_EOL;
        $source .= '    <h3><a href="'.$this->data['Url'].'" target="_blank"><img src="'.$this->data['CacheType']['ImageURL'].'" alt="'.$this->data['CacheType']['GeocacheTypeName'].'" style="padding-right: 10px">'.$this->data['Name'].'</a></h3>'.PHP_EOL;
        $source .= '  </div>'.PHP_EOL;
        $source .= '  <div class="col-lg-2 col-md-3 col-xs-4 text-right">'.PHP_EOL;
        $source .= '    <h3>'.$this->data['Code'].'</h3>'.PHP_EOL;
        $source .= '  </div>'.PHP_EOL;
        $source .= '</div>'.PHP_EOL;
        $source .= '<p>Owner: '.$this->data['Owner']['UserName'].'</p>'.PHP_EOL;
        $source .= '<p>Owner HideCount: '.$this->data['Owner']['HideCount'].'</p>'.PHP_EOL;
        $source .= '<p>Owner FindCount: '.$this->data['Owner']['FindCount'].'</p>'.PHP_EOL;
        $source .= '<p>PlacedBy: '.$this->data['PlacedBy'].'</p>'.PHP_EOL;
        $source .= '<p>ContainerTypeName: '.$this->data['ContainerType']['ContainerTypeName'].'</p>'.PHP_EOL;
        $source .= '<p>Country: '.$this->data['Country'].'</p>'.PHP_EOL;
        $source .= '<p>Difficulty: '.$this->data['Difficulty'].'</p>'.PHP_EOL;
        $source .= '<p>Terrain: '.$this->data['Terrain'].'</p>'.PHP_EOL;
        $source .= '<p>Hints: '.$this->data['EncodedHints'].'</p>'.PHP_EOL;
        $source .= '<p>FavoritePoints: '.$this->data['FavoritePoints'].'</p>'.PHP_EOL;
        $source .= '<p>Latitude: '.$this->data['Latitude'].'</p>'.PHP_EOL;
        $source .= '<p>Longitude: '.$this->data['Longitude'].'</p>'.PHP_EOL;

        return $source;
    }
}
