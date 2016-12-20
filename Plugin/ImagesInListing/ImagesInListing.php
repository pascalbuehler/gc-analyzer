<?php
namespace Plugin\ImagesInListing;

class ImagesInListing extends \Plugin\AbstractPlugin {
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
        if(count($this->images)>0) {
            $this->setSuccess(true);
        }
    }

    public function getResult() {
        return [
            'images' => $this->images,
        ];
    }

    public function getOutput() {
        $source = '';
        if(count($this->images)>0) {
            foreach($this->images as $fieldname => $images) {
                $source.= '<h4>'.$fieldname.'</h4>'.PHP_EOL;

                foreach($images as $image) {
                    $source.= '<a href="'.$image.'">'.$image.'</a>'.PHP_EOL;
                }
            }
        }

        return $source;
    }
}
