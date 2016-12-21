<?php
namespace Plugin\ImageInfo;

class ImageInfo extends \Plugin\AbstractPlugin {
    private $imagesWithInfo = [];

    public function calculate() {
        foreach($this->parameters['imageSources'] as $imageListName) {
            $imageListFields = $this->data['plugins'][$imageListName];

            foreach ($imageListFields as $images)
            {
                foreach ($images as $image)
                {
                    $size = getimagesize($image);

                    $imageInfo = array();
                    $imageInfo['source'] = $imageListName;
                    $imageInfo['url'] = $image;
                    $imageInfo['width'] = $size[0];
                    $imageInfo['height'] = $size[1];
                    $imageInfo['mime'] = $size['mime'];

                    if ($size[2] == IMG_JPEG)
                    {
                        $exif = exif_read_data($image, 'IFD0');
                        if ($exif != null && $exif != '')
                        {
                            $imageInfo['exif'] = $exif;
                            $this->setSuccess(true);
                        }
                    }

                    if (!array_key_exists($image, $this->imagesWithInfo))
	                {
                        $this->imagesWithInfo[$image] = $imageInfo;
                    } else {
                        //image exists in the list, add current source info
                        $this->imagesWithInfo[$image]['source'] .= ' / '.$imageInfo['source'];
                    }
                }
            }
        }
    }

    public function getResult() {
        return [
            'imagesWithInfo' => $this->imagesWithInfo,
        ];
    }

    public function getOutput() {
        $source = '';
        if(count($this->imagesWithInfo)>0) {
            foreach($this->imagesWithInfo as $imageWithInfo) {
                $source.= '<div class="row">'.PHP_EOL;
                $source.= '  <div class="col-lg-6 limit-img">'.PHP_EOL;
                $source.= '    <h5>Image from '.$imageWithInfo['source'].'</h5>'.PHP_EOL;
                $source.= '    <div class="well">'.PHP_EOL;
                $source.= '      <img src="'.$imageWithInfo['url'].'" /><br />'.PHP_EOL;
                $source.= '      Image: '.$imageWithInfo['url'].'<br />'.PHP_EOL;
                $source.= '      Width: '.$imageWithInfo['width'].'<br />'.PHP_EOL;
                $source.= '      Height: '.$imageWithInfo['height'].'<br />'.PHP_EOL;
                $source.= '      Mime-Type: '.$imageWithInfo['mime'].'<br />'.PHP_EOL;
                $source.= '    </div>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;

                if (isset($imageWithInfo['exif'])){
                    $exifPrinted = print_r($imageWithInfo['exif'],true);

                    $source.= '  <div class="col-lg-6">'.PHP_EOL;
                    $source.= '    <h5>EXIF</h5>'.PHP_EOL;
                    $source.= '    <pre class="pre-scrollable">'.$exifPrinted.'</pre>'.PHP_EOL;
                    $source.= '  </div>'.PHP_EOL;
                }
                $source.= '</div>'.PHP_EOL;
            }
        }

        return $source;
    }
}