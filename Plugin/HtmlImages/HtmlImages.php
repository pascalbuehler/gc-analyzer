<?php
namespace Plugin\HtmlImages;

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
                
                if (!(\Helper\CheckerHelper::isCheckerUrl($url)))
                {
                    if (!isset($this->images[$field]) || !in_array($url, $this->images[$field]))
                    {
                        $name = $element->getAttribute('title') ? $element->getAttribute('title') : ($element->getAttribute('alt') ? $element->getAttribute('alt') : '');

                        $imageModel = new \Model\ImageModel();
                        $imageModel->url = $url;
                        $imageModel->name = $name;
                        $imageModel->description = '';
                        $imageModel->base64 = \Helper\ImageBase64Helper::downloadImageAsBase64($url);

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