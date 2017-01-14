<?php
namespace Plugin\ListingImages;

class ListingImages extends \Plugin\AbstractPlugin {
    private $images = [];

    public function calculate() {
        foreach($this->parameters['fields'] as $field) {
            foreach ($this->data[$field] as $image)
            {
                $url = $image['Url'];
                if (\Helper\KnownUrlHelper::isCheckerUrl($url)) continue;
                $imageModel = new \Model\ImageModel();
                $imageModel->url = $url;
                $imageModel->name = $image['Name'];
                $imageModel->description = $image['Description'];
                $imageModel->base64 = \Helper\ImageBase64Helper::downloadImageAsBase64($url);

                $this->images[$field][] = $imageModel;
            }
        }
    }

    public function getResult() {
        return $this->images;
    }
}