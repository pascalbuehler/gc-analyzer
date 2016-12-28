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
                    'Intensiv colors' => array(255, IMG_FILTER_COLORIZE, 0, 0, 0),
                    'Colorize red' => array(50, IMG_FILTER_COLORIZE, 0, -250, -250),
                    'Colorize green' => array(50, IMG_FILTER_COLORIZE, -250, 0, -250),
                    'Colorize blue' => array(50, IMG_FILTER_COLORIZE, -255, -255, 0)
                );
                
                foreach ($filtersToApply as $filterText => $filter)
                {
                    if ($imageWithInfo->mime == 'image/png') {
                        $im = imagecreatefrompng($imageWithInfo->url);
                    } else if ($imageWithInfo->mime == 'image/gif') {
                        $im = imagecreatefromgif($imageWithInfo->url);
                    } else if ($imageWithInfo->mime == 'image/jpeg') {
                        $im = imagecreatefromjpeg($imageWithInfo->url);
                    }
                    
                    $extremColors = $filter[0];
                    $filterType = $filter[1];
                    
                    $imageFilterResult = false;
                    $imageFilterResult = imagefilter($im, $filterType, $filter[2], $filter[3], $filter[4]);
                    $this->intensivyColors($im, $extremColors);
                    
                    if($im && $imageFilterResult)
                    {
                        $newFileName = 'Temp/'.$this->data['Code'].'_'.$i.'_'.$filterText.'.png';
                        imagepng($im, $newFileName);
                        
                        $result['imagesWithFilters'][$filterText] = $newFileName;
                    }

                    imagedestroy($im);
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

                imagesetpixel($im, $x, $y,imagecolorallocate($im, $r, $g, $b)); 
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