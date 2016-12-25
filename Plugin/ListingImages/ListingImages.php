<?php
namespace Plugin\ListingImages;
use Plugin\ImageInfo as ImageInfoPlugin;

class ListingImages extends \Plugin\AbstractPlugin {
    private $images = [];

    public function calculate() {
        foreach($this->parameters['fields'] as $field) {
            foreach ($this->data[$field] as $image)
            {
				$imageModel = new ImageInfoPlugin\ImageModel();
				$imageModel->url = $image['Url'];
                $imageModel->name = $image['Name'];
                $imageModel->description = $image['Description'];

                $this->images[$field][] = $imageModel;
            }
        }
    }

    public function getResult() {
        return $this->images;
    }
}
