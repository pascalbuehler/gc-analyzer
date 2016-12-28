<?php
namespace Plugin\ImageFilters;

class ImageFilters extends \Plugin\AbstractPlugin {
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
                    'Randomize colorpalette',
                    'Colorize red',
                    'Colorize green',
                    'Colorize blue',
                );
                
                foreach ($filtersToApply as $filterText)
                {
                    $im = \Helper\ImageBase64Helper::createImageResourceFromBase64($imageWithInfo->base64);
                                        
                    switch ($filterText)
                    {
                        case 'Intensive colors':
                            $this->intensivyColors($im, 255);
                            break;
                        case 'Randomize colorpalette':
                            for ($index = 0; $index <= imagecolorstotal($im); $index++)
                            {
                                imagecolorset($im, $index, rand(0, 255), rand(0, 255), rand(0, 255));
                            }
                            break;
                        case 'Colorize red':
                            imagefilter($im, IMG_FILTER_COLORIZE, 0, -250, -250);
                            $this->intensivyColors($im, 50);
                            break;
                        case 'Colorize green':
                            imagefilter($im, IMG_FILTER_COLORIZE, -250, 0, -250);
                            $this->intensivyColors($im, 50);
                            break;
                        case 'Colorize blue':
                            imagefilter($im, IMG_FILTER_COLORIZE, -250, -250, 0);
                            $this->intensivyColors($im, 50);
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
                
                if ($r > 255) $r = 255;
                if ($g > 255) $g = 255;
                if ($b > 255) $b = 255;

                imagesetpixel($im, $x, $y, imagecolorallocate($im, $r, $g, $b)); 
            }
        }
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