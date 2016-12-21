<?php
namespace Plugin\ListingImages;

class ListingImages extends \Plugin\AbstractPlugin {
    private $images = [];

    public function calculate() {
        foreach($this->parameters['fields'] as $field) {
            foreach ($this->data[$field] as $image)
            {
                $this->images[$field][] = $image['Url'];
            }
        }
    }

    public function getResult() {
        return $this->images;
    }
}
