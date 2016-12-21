<?php
namespace Plugin\HtmlLinks;

class HtmlLinks extends \Plugin\AbstractPlugin {
    private $links = [];

    public function calculate() {
        foreach($this->parameters['fields'] as $field) {
            $html = trim($this->data[$field]);
            if(!strlen($html)>0) {
                continue;
            }

            $doc = new \DOMDocument();
            $doc->loadHTML($this->data[$field]);

            $elements = $doc->getElementsByTagName('a');

            foreach ($elements as $element)
            {
                $href = $element->getAttribute('href');
                if (!isset($this->links[$field]) || !in_array($img, $this->links[$field]))
                {
                    $this->links[$field][] = $href;
                }
            }
        }
    }

    public function getResult() {
        return $this->links;
    }

    public function getOutput() {
        $source = '';
        foreach($this->links as $field => $links) {
            $source.= '<h4>'.$field.'</h4>'.PHP_EOL;
            foreach($links as $link) {
                $source.= '<p><a href="'.$link.'" target="_blank">'.$link.'</a></p>'.PHP_EOL;

                if (strpos($link, 'geocheck.org') > -1)
                {
                    $source.= '<p>Image from geocheck.org: <img alt="GeoCheck.org" src="http://geocheck.org/geocheck_small.php?gid='.substr($link, strpos($link, 'gid=')+4).'" /></p>';
                }
                if (strpos($link, 'geochecker.com') > -1 && strpos($link, 'visitcount') <= 0)
                {
                    $source.= '<p>With count of geochecker.com: <a href="'.$link.'&visitcount=1" target="blank">'.$link.'&visitcount=1</a></p>';
                }
            }
        }

        return $source;
    }
}
