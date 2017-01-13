<?php
namespace Plugin\ImageFilters;

class ImageFilters extends \Plugin\AbstractPlugin {
    const IMAGE_SIZE_MEDIUM = 640;
    const IMAGE_SIZE_SMALL = 320;
    
    private $imagesWithFilters = [];

    public function calculate() {
        foreach($this->parameters['imageSources'] as $imageListName) {
            $i = 0;
            
            foreach ($this->data['plugins'][$imageListName] as $imageWithInfo)
            {
                $i++;
                
                $result = array();
                $result['imageWithInfo'] = $imageWithInfo;

                $filtersToApply = array(
                    'Intensive colors',
                    'Random colors',
                    'Checker colors',
                    'Edge detect',
                );
                
                foreach ($filtersToApply as $filterText)
                {
                    $im = \Helper\ImageBase64Helper::createImageResourceFromBase64($imageWithInfo->base64);
                    
                    switch ($filterText)
                    {
                         case 'Intensive colors':
                            if($imageWithInfo->width>self::IMAGE_SIZE_SMALL) {
                                $im = imagescale($im, self::IMAGE_SIZE_SMALL);
                            }
                            $this->intensivyColors($im, 225);
                            break;
                        case 'Random colors':
                            if($imageWithInfo->width>self::IMAGE_SIZE_MEDIUM) {
                                $im = imagescale($im, self::IMAGE_SIZE_MEDIUM);
                            }
                            
                            // save to gif and reload it
                            $base64gif = \Helper\ImageBase64Helper::encodeImageResourceToGifBase64($im);
                            $im = \Helper\ImageBase64Helper::createImageResourceFromBase64($base64gif);

                            for ($index = 0; $index < imagecolorstotal($im); $index++) {
                                imagecolorset($im, $index, rand(0, 255), rand(0, 255), rand(0, 255));
                            }
                            break;
                        case 'Checker colors':
                            if($imageWithInfo->width>self::IMAGE_SIZE_MEDIUM) {
                                $im = imagescale($im, self::IMAGE_SIZE_MEDIUM);
                            }
                            
                            // save to gif and reload it
                            $base64gif = \Helper\ImageBase64Helper::encodeImageResourceToGifBase64($im);
                            $im = \Helper\ImageBase64Helper::createImageResourceFromBase64($base64gif);

                            $colors = [];
                            for ($index = 0; $index < imagecolorstotal($im); $index++) {
                                $color = imagecolorsforindex($im, $index);
                                $colors[$index] = str_pad(dechex($color['red']), 2, 0, STR_PAD_LEFT).
                                    str_pad(dechex($color['green']), 2, 0, STR_PAD_LEFT).
                                    str_pad(dechex($color['blue']), 2, 0, STR_PAD_LEFT);
                            }
                            asort($colors);

                            $brightness = 0;
                            foreach($colors as $index => $color) {
                                imagecolorset($im, $index, $brightness, $brightness, $brightness);
                                $brightness = $brightness==0 ? 255 : 0;
                            }
                            break;
                        case 'Edge detect':
                            if($imageWithInfo->width>self::IMAGE_SIZE_MEDIUM) {
                                $im = imagescale($im, self::IMAGE_SIZE_MEDIUM);
                            }
                            
                            $this->edgeDetect($im);
                            $this->enhanceContrast($im, 3, -90);
                            break;
                    }
                    
                    $base64data = \Helper\ImageBase64Helper::encodeImageResourceToPngBase64($im);
                    imagedestroy($im);
                    
                    $dataUri = 'data:image/png;base64,'.$base64data;
                    $result['imagesWithFilters'][$filterText] = $dataUri;
                }

                $this->imagesWithFilters[] = $result;
            }
        }
    }
    
    private function enhanceContrast(&$im, $repeat = 1, $contrast = -100) {
        for($i=0; $i<$repeat; $i++) {
            imagefilter($im, IMG_FILTER_CONTRAST, $contrast);
        }
    }
    
     private function intensivyColors(&$im, $factor){ 
        $height = imagesy($im); 
        $width = imagesx($im); 
        for($x=0; $x<$width; $x++){ 
            for($y=0; $y<$height; $y++){ 
                $rgb = ImageColorAt($im, $x, $y);
                $r = ($rgb >> 16) & 0xFF; 
                $g = ($rgb >> 8) & 0xFF; 
                $b = $rgb & 0xFF; 
                
                $r = $r*$factor;
                $g = $g*$factor;
                $b = $b*$factor;
                
                $r = min($r, 255);
                $g = min($g, 255);
                $b = min($b, 255);
                imagesetpixel($im, $x, $y, imagecolorallocate($im, $r, $g, $b)); 
            }
        }
    }
    
    private function edgeDetect(&$im) { 
        imagefilter($im, IMG_FILTER_EDGEDETECT);
    }

    public function getResult() {
        return $this->imagesWithFilters;
    }

    public function getOutput() {
        $source = '';
        if(count($this->imagesWithFilters)>0)
        {
            foreach($this->imagesWithFilters as $imagesWithFilters)
            {
                $imageWithInfo = $imagesWithFilters['imageWithInfo'];
                $imagesWithFilters = $imagesWithFilters['imagesWithFilters'];
                
                $source.= '<div class="row">'.PHP_EOL;
                $source.= '  <div class="col-lg-6 limit-img">'.PHP_EOL;
                $source.= '    <h4>'.$imageWithInfo->url.'</h4>'.PHP_EOL;
                
                foreach ($imagesWithFilters as $filterName => $imageResult)
                {
                    $source.= '    <div class="thumbnail">'.PHP_EOL;
                    $source.= '      <h5>'.$filterName.'</h5>'.PHP_EOL;
                    $source.= '      <img src="'.$imageResult.'" /><br />'.PHP_EOL;
                    
                    $source.= '    </div>'.PHP_EOL;
                }
                
                $source.= '  </div>'.PHP_EOL;
                $source.= '</div>'.PHP_EOL;
            }
        }

        return $source;
    }
}