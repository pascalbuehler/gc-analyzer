<?php
namespace Plugin\HtmlLinks;

class HtmlLinks extends \Plugin\AbstractPlugin {
    private $links = [];
    private $images = [];

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
                if(substr($href, 0, 4)!=='http') {
                    continue;
                }
                
                if (!isset($this->links[$field]) || !array_key_exists($href, $this->links[$field]))
                {
                    $type = $this->getHeaderContentType($href);
                    $link = new \Model\LinkModel();
                    $link->url = $href;
                    $link->contentType = $type;
                    $this->links[$field][$href] = $link;

                    if (strpos($type, 'image') !== false)
                    {
                        
                        $imageModel = new \Model\ImageModel();
                        $imageModel->url = $href;
                        $imageModel->name = '';
                        $imageModel->description = '';
                        $imageModel->base64 = \Helper\ImageBase64Helper::downloadImageAsBase64($href);
                        $this->images[$field][] = $imageModel;
                    }
                }
            }
        }
    }

    private function getHeaderContentType($href) {
        // get headers, if not a checker-url
        if(\Helper\KnownUrlHelper::isCheckerUrl($href) || \Helper\KnownUrlHelper::isGroundspeakUrl($href)) {
            return '';
        }
        
        $resCurl = curl_init($href);
        curl_setopt($resCurl, CURLOPT_HEADER, true);
        curl_setopt($resCurl, CURLOPT_NOBODY, true);
        curl_setopt($resCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($resCurl, CURLOPT_FOLLOWLOCATION, true);
        $headers = curl_exec($resCurl);
        curl_close($resCurl);

        $res = preg_match_all('/content-type:(.+)/', strtolower($headers), $type);
        if($res && isset($type[1]) && is_array($type[1]) && count($type[1])>0) {
            $type = $type[1][0];
        }
        
        return $type;
    }
    
    public function getResult() {
        return array(
            'Links' => $this->links,
            'Images' => $this->images,
        );
    }

    public function getOutput() {
        $source = '';
        foreach($this->links as $field => $links)
        {
            $source.= '<h4>'.$field.'</h4>'.PHP_EOL;
            foreach($links as $link)
            {
                $source.= '<p><a href="'.$link->url.'" target="_blank">'.$link->url.'</a> '.$link->contentType.'</p>'.PHP_EOL;

                if (strpos($link->url, 'geocheck.org') > -1)
                {
                    $source.= '<p>Image from geocheck.org: <img alt="GeoCheck.org" src="http://geocheck.org/geocheck_small.php?gid='.substr($link->url, strpos($link->url, 'gid=')+4).'" /></p>';
                }
                if (strpos($link->url, 'geochecker.com') > -1 && strpos($link->url, 'visitcount') <= 0)
                {
                    $source.= '<p>With count of geochecker.com: <a href="'.$link->url.'&visitcount=1" target="blank">'.$link->url.'&visitcount=1</a></p>';
                }
            }
        }

        return $source;
    }
}