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

                    $this->imagesWithInfo[$imageListName][] = $imageInfo;
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
            foreach($this->imagesWithInfo as $imageListName => $imagesWithInfo) {
                $source.= '<h4>'.$imageListName.'</h4>'.PHP_EOL;

                foreach($imagesWithInfo as $imageWithInfo) {
                    $source.= 'Image: '.$imageWithInfo['url'].'<br />'.PHP_EOL;
                    $source.= 'Width: '.$imageWithInfo['width'].'<br />'.PHP_EOL;
                    $source.= 'Height: '.$imageWithInfo['height'].'<br />'.PHP_EOL;
                    $source.= 'Mime-Type: '.$imageWithInfo['mime'].'<br />'.PHP_EOL;

                    if (isset($imageWithInfo['exif'])){
                        $exifPrinted = print_r($imageWithInfo['exif'],true);
                        $source.= 'Exif: <pre class="pre-scrollable">'.$exifPrinted.'</pre>'.PHP_EOL;
                    }
                }
            }
        }

        return $source;
    }
}