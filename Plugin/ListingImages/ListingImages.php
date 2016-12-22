<?php
namespace Plugin\ListingImages;

class ListingImages extends \Plugin\AbstractPlugin {
    private $images = [];

    public function calculate() {
        foreach($this->parameters['fields'] as $field) {
            foreach ($this->data[$field] as $image)
            {
                $this->images[$field][] = [
                    'Url' => $image['Url'],
                    'Name' => $image['Name'],
                    'Description' => $image['Description'],
                ];
            }
        }
    }

    public function getResult() {
        return $this->images;
    }
}
