<?php
namespace Plugin\ImageInfo;

class ImageInfo extends \Plugin\AbstractPlugin {
    private $imagesWithInfo = [];

    public function calculate() {
        foreach($this->parameters['imageSources'] as $imageListNameRaw) {
            $explode = explode('::', $imageListNameRaw);
            $imageListName = $explode[0];

            if (isset($explode[1]))
            {
                $imageListFields = $this->data['plugins'][$imageListName][$explode[1]];
            } else {
                $imageListFields = $this->data['plugins'][$imageListName];
            }

            foreach ($imageListFields as $images) {
                foreach ($images as $imageModel) {
                    
                    $url = $imageModel->url;

                    $imgKey = md5($imageModel->base64);
                    
                    if (!array_key_exists($imgKey, $this->imagesWithInfo))
                    {
                        $imageWithInfoModel = new \Model\ImageWithInfoModel();
                        $imageWithInfoModel->url = $imageModel->url;
                        $imageWithInfoModel->name = $imageModel->name;
                        $imageWithInfoModel->description = $imageModel->description;
                        $imageWithInfoModel->base64 = $imageModel->base64;
                        
                        $size = getimagesizefromstring(base64_decode($imageModel->base64));
                        $imageWithInfoModel->source = $imageListName;
                        $imageWithInfoModel->width = $size[0];
                        $imageWithInfoModel->height = $size[1];
                        $imageWithInfoModel->mime = $size['mime'];

                        if ($size[2] == IMAGETYPE_JPEG) {
                            $exif = exif_read_data($imageWithInfoModel->getImgSrcBase64(), 'FILE', true);
                            if ($exif != null && $exif != '') {
                                $imageWithInfoModel->exif = $exif;
                                $this->setSuccess(true);
                            }
                        }
                        
                        if ($size[2] == IMAGETYPE_GIF) {
                            $im = \Helper\ImageBase64Helper::createImageResourceFromBase64($imageModel->base64);
                            $imageWithInfoModel->imagecolorstotal = imagecolorstotal($im);
                            imagedestroy($im);
                        }

                        $this->imagesWithInfo[$imgKey] = $imageWithInfoModel;
                    }
                    else {
                        //image exists in the list, add current source info
                        $this->imagesWithInfo[$imgKey]->source .= ' / '.$imageListName;

                        if($imageModel->name != null && strlen($imageModel->name) > 0) {
                            if($this->imagesWithInfo[$imgKey]->name != null && strlen($this->imagesWithInfo[$imgKey]->name)>0) {
                                $this->imagesWithInfo[$imgKey]->name.= ' / '.$imageModel->name;
                            }
                            else {
                                $this->imagesWithInfo[$imgKey]->name = $imageModel->name;
                            }
                        }

                        if($imageModel->name != null && strlen($imageModel->description) > 0) {
                            if($this->imagesWithInfo[$imgKey]->description != null && strlen($this->imagesWithInfo[$imgKey]->description)>0) {
                                $this->imagesWithInfo[$imgKey]->description.= ' / '.$imageModel->description;
                            }
                            else {
                                $this->imagesWithInfo[$imgKey]->description = $imageModel->description;
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
        if(count($this->imagesWithInfo)>0)
        {
            foreach($this->imagesWithInfo as $imageWithInfo)
            {
                $source.= '<div class="row">'.PHP_EOL;
                $source.= '  <div class="col-lg-6 limit-img">'.PHP_EOL;
                $source.= '    <h4>Image from '.$imageWithInfo->source.'</h4>'.PHP_EOL;
                $source.= '    <div class="thumbnail">'.PHP_EOL;
                if($imageWithInfo->name != null && strlen($imageWithInfo->name)>0) {
                    $source.= '      <h5>'.$imageWithInfo->name.'</h5>'.PHP_EOL;
                }
                $source.= '      <img src="'.$imageWithInfo->getImgSrcBase64().'" /><br />'.PHP_EOL;
                $source.= '      <div class="caption">'.PHP_EOL;
                $source.= '        <table class="table table-condensed">'.PHP_EOL;
                $source.= '          <tr><td valign="top" width="100">Url</td><td><a href="'.$imageWithInfo->url.'" target="_blank">'.$imageWithInfo->url.'</a></td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Width</td><td>'.$imageWithInfo->width.'</td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Height</td><td>'.$imageWithInfo->height.'</td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Mime-Type</td><td>'.$imageWithInfo->mime.'</td></tr>'.PHP_EOL;
                
                if($imageWithInfo->imagecolorstotal != null) {
                    $source.= '          <tr><td valign="top">Count of colors</td><td>'.$imageWithInfo->imagecolorstotal.' <a href="#" data-toggle="tooltip" data-placement="top" title="Interesting to check, if something is hidden in the picture."><span class="glyphicon glyphicon-question-sign"></span></a></td></tr>'.PHP_EOL;
                }
                
                if($imageWithInfo->description != null && strlen($imageWithInfo->description)>0) {
                    $source.= '          <tr><td valign="top">Description</td><td>'.$imageWithInfo->description.'</td></tr>'.PHP_EOL;
                }
                
                $source.= '          <tr><td valign="top" colspan="2">'.PHP_EOL;
                $source.= '            <a href="https://www.google.com/searchbyimage?&image_url='.$imageWithInfo->url.'"><span class="glyphicon glyphicon-search"></span>Google Image</a>&nbsp;'.PHP_EOL;
                $source.= '            <a href="http://exif.regex.info/exif.cgi?imgurl='.$imageWithInfo->url.'"><span class="glyphicon glyphicon-search"></span>Jeffreys (exif)</a>'.PHP_EOL;
               $source.= '           </td></tr>'.PHP_EOL;
                $source.= '        </table>'.PHP_EOL;
                $source.= '      </div>'.PHP_EOL;
                $source.= '    </div>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;

                if ($imageWithInfo->exif != null){
                    $source.= '  <div class="col-lg-6">'.PHP_EOL;
                    $source.= '    <h4>EXIF</h4>'.PHP_EOL;
                    $source.= '    <pre class="pre-scrollable">'.print_r($imageWithInfo->exif, true).'</pre>'.PHP_EOL;
                    $source.= '  </div>'.PHP_EOL;
                }
                
                $source.= '</div>'.PHP_EOL;
            }
        }

        return $source;
    }
}