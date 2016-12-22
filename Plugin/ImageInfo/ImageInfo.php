<?php
namespace Plugin\ImageInfo;

class ImageInfo extends \Plugin\AbstractPlugin {
    private $imagesWithInfo = [];

    public function calculate() {
        foreach($this->parameters['imageSources'] as $imageListName) {
            $imageListFields = $this->data['plugins'][$imageListName];

            foreach ($imageListFields as $images) {
                foreach ($images as $image) {
                    var_dump($image);
                    $size = getimagesize($image['Url']);

                    $image['Source'] = $imageListName;
                    $image['Width'] = $size[0];
                    $image['Height'] = $size[1];
                    $image['Mime'] = $size['mime'];

                    if ($size[2] == IMG_JPEG) {
                        $exif = exif_read_data($image['Url'], 'IFD0');
                        if ($exif != null && $exif != '') {
                            $image['Exif'] = $exif;
                            $this->setSuccess(true);
                        }
                    }

                    if (!array_key_exists($image['Url'], $this->imagesWithInfo)) {
                        $this->imagesWithInfo[$image['Url']] = $image;
                    }
                    else {
                        //image exists in the list, add current source info
                        $this->imagesWithInfo[$image['Url']]['Source'] .= ' / '.$image['Source'];
                        if(strlen($image['Name'])>0) {
                            if(strlen($this->imagesWithInfo[$image['Url']]['Name'])>0) {
                                $this->imagesWithInfo[$image['Url']]['Name'].= ' / '.$image['Name'];
                            }
                            else {
                                $this->imagesWithInfo[$image['Url']]['Name'] = $image['Name'];
                            }
                        }
                    }
                }
            }
        }
    }

    public function getResult() {
        return $this->imagesWithInfo;
    }

    public function getOutput() {
        $source = '';
        if(count($this->imagesWithInfo)>0) {
            foreach($this->imagesWithInfo as $imageWithInfo) {
                $source.= '<div class="row">'.PHP_EOL;
                $source.= '  <div class="col-lg-6 limit-img">'.PHP_EOL;
                $source.= '    <h4>Image from '.$imageWithInfo['Source'].'</h4>'.PHP_EOL;
                $source.= '    <div class="thumbnail">'.PHP_EOL;
                if(isset($imageWithInfo['Name']) && strlen($imageWithInfo['Name'])>0) {
                    $source.= '      <h5>'.$imageWithInfo['Name'].'</h5>'.PHP_EOL;
                }
                $source.= '      <img src="'.$imageWithInfo['Url'].'" /><br />'.PHP_EOL;
                $source.= '      <div class="caption">'.PHP_EOL;
                $source.= '        <table class="table table-condensed">'.PHP_EOL;
                $source.= '          <tr><td valign="top" width="100">Url</td><td><a href="'.$imageWithInfo['Url'].'" target="_blank">'.$imageWithInfo['Url'].'</a></td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Width</td><td>'.$imageWithInfo['Width'].'</td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Height</td><td>'.$imageWithInfo['Height'].'</td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Mime-Type</td><td>'.$imageWithInfo['Mime'].'</td></tr>'.PHP_EOL;
                if(isset($imageWithInfo['Description']) && strlen($imageWithInfo['Description'])>0) {
                    $source.= '          <tr><td valign="top">Description</td><td>'.$imageWithInfo['Description'].'</td></tr>'.PHP_EOL;
                }
                $source.= '        </table>'.PHP_EOL;
                $source.= '      </div>'.PHP_EOL;
                $source.= '    </div>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;

                if (isset($imageWithInfo['Exif'])){
                    $source.= '  <div class="col-lg-6">'.PHP_EOL;
                    $source.= '    <h4>EXIF</h4>'.PHP_EOL;
                    $source.= '    <pre class="pre-scrollable">'.print_r($imageWithInfo['Exif'], true).'</pre>'.PHP_EOL;
                    $source.= '  </div>'.PHP_EOL;
                }
                $source.= '</div>'.PHP_EOL;
            }
        }

        return $source;
    }
}