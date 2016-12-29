<?php
namespace Plugin\BackgroundImages;

class BackgroundImages extends \Plugin\AbstractPlugin {
    private $images = [];

    public function calculate() {
        
        if(isset($this->data['isPremium']) && $this->data['isPremium']===true) {
            return;
        }
        
        $url = $this->data['Url'];
        $listing = file_get_contents($url);
        
        $matches = false;
        preg_match_all('/background="(.*?)"/', $listing, $matches);
        if(is_array($matches[1]) && count($matches[1])>0) {
            foreach($matches[1] as $match) {
                if(trim($match)!='') {
                    $imageModel = new \Model\ImageModel();
                    $imageModel->url = $match;
                    $imageModel->name = '';
                    $imageModel->description = '';
                    $imageModel->base64 = \Helper\ImageBase64Helper::downloadImageAsBase64($match);

                    $this->images['BackgroundImages'][] = $imageModel;
                }
            }
        }
    }

    public function getResult() {
        return $this->images;
    }
}
