<?php
namespace Plugin\HtmlImages;
use Plugin\ImageInfo as ImageInfoPlugin;

class HtmlImages extends \Plugin\AbstractPlugin {
    private $images = [];

    public function calculate() {
        foreach($this->parameters['fields'] as $field) {
            $html = trim($this->data[$field]);
            if(!strlen($html)>0) {
                continue;
            }
            
            $doc = new \DOMDocument();
            $doc->loadHTML($this->data[$field]);

            $elements = $doc->getElementsByTagName('img');

            foreach ($elements as $element)
            {
                $url = $element->getAttribute('src');
                
                if (!(strpos($url, 'geocheck.org') > -1 || strpos($url, 'geochecker.com') > -1))
                {
                    if (!isset($this->images[$field]) || !in_array($url, $this->images[$field]))
                    {
                        $name = $element->getAttribute('title') ? $element->getAttribute('title') : ($element->getAttribute('alt') ? $element->getAttribute('alt') : '');

                        $imageModel = new \Model\ImageModel();
                        $imageModel->url = $url;
                        $imageModel->name = $name;
                        $imageModel->description = '';

                        $this->images[$field][] = $imageModel;
                    }
                }
            }
        }
    }

    public function getResult() {
        return $this->images;
    }
}
