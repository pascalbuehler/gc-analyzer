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

        $source .= '<p>Name: '.$this->data['Name'].'</p>';
        $source .= '<p>Owner: '.$this->data['Owner']['UserName'].'</p>';
        $source .= '<p>Owner HideCount: '.$this->data['Owner']['HideCount'].'</p>';
        $source .= '<p>Owner FindCount: '.$this->data['Owner']['FindCount'].'</p>';
        $source .= '<p>PlacedBy: '.$this->data['PlacedBy'].'</p>';
        $source .= '<p>GeocacheTypeName: '.$this->data['CacheType']['GeocacheTypeName'].'</p>';
        $source .= '<p>ContainerTypeName: '.$this->data['ContainerType']['ContainerTypeName'].'</p>';
        $source .= '<p>Country: '.$this->data['Country'].'</p>';
        $source .= '<p>Difficulty: '.$this->data['Difficulty'].'</p>';
        $source .= '<p>Terrain: '.$this->data['Terrain'].'</p>';
        $source .= '<p>Hints: '.$this->data['EncodedHints'].'</p>';
        $source .= '<p>FavoritePoints: '.$this->data['FavoritePoints'].'</p>';
        $source .= '<p>Latitude: '.$this->data['Latitude'].'</p>';
        $source .= '<p>Longitude: '.$this->data['Longitude'].'</p>';

        return $source;
    }
}
