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
                
                addImageToArray($field, $url, $image['Name'], $image['Description']);                
            }
        }
        
        $avatarImageUrl = $this->data["Owner"]["AvatarUrl"];
        $this->addImageToArray("owner_avatar", $avatarImageUrl, 'Avatar of Owner', '');
    }

    public function getResult() {
        return $this->images;
    }
    
    private function addImageToArray($key, $url, $name, $description) {
        $imageModel = new \Model\ImageModel();
        $imageModel->url = $url;
        $imageModel->name = $name;
        $imageModel->description = $description;
        $imageModel->base64 = \Helper\ImageBase64Helper::downloadImageAsBase64($url);
        
        $this->images[$key][] = $imageModel;
    }
}