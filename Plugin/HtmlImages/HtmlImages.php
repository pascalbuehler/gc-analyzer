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
                if (!isset($this->images[$field]) || !in_array($url, $this->images[$field]))
                {
                    $name = $element->getAttribute('title') ? $element->getAttribute('title') : ($element->getAttribute('alt') ? $element->getAttribute('alt') : '');
                    $this->images[$field][] = [
                        'Url' => $url,
                        'Name' => $name,
                        'Description' => '',
                    ];
                }
            }
        }
    }

    public function getResult() {
        return $this->images;
    }
}
