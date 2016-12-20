<?php
namespace Plugin\HtmlImages;

class HtmlImages extends \Plugin\AbstractPlugin {
    private $images = [];

    public function calculate() {
        foreach($this->parameters['fields'] as $field) {

			$doc = new \DOMDocument();
			$doc->loadHTML($this->data[$field]);

			$elements = $doc->getElementsByTagName('img');

			foreach ($elements as $element)
			{
				$img = $element->getAttribute('src');
				if (!isset($this->images[$field]) || !in_array($img, $this->images[$field]))
				{
					$this->images[$field][] = $img;
				}
			}
        }
    }

    public function getResult() {
        return $this->images;
    }

    public function getOutput() {
		return '';
    }
}
