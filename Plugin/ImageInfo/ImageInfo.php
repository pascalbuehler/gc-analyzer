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

					if (!array_key_exists($url, $this->imagesWithInfo)) {

	                    $size = getimagesize($url);
						if ($size)
						{
							$imageWithInfoModel = new ImageWithInfoModel();
                            $imageWithInfoModel->url = $imageModel->url;
                            $imageWithInfoModel->name = $imageModel->name;
                            $imageWithInfoModel->description = $imageModel->description;

                            $imageWithInfoModel->source = $imageListName;
		                    $imageWithInfoModel->width = $size[0];
		                    $imageWithInfoModel->height = $size[1];
		                    $imageWithInfoModel->mime = $size['mime'];

		                    if ($size[2] == IMG_JPEG) {
		                        $exif = exif_read_data($imageModel->url, 'IFD0');
		                        if ($exif != null && $exif != '') {
		                            $imageWithInfoModel->exif = $exif;
		                            $this->setSuccess(true);
		                        }
		                    }

	                        $this->imagesWithInfo[$url] = $imageWithInfoModel;
						}
                    }
                    else {
                        //image exists in the list, add current source info
                        $this->imagesWithInfo[$url]->source .= ' / '.$imageListName;

                        if($imageModel->name != null && strlen($imageModel->name) > 0) {
                            if($this->imagesWithInfo[$url]->name != null && strlen($this->imagesWithInfo[$url]->name)>0) {
                                $this->imagesWithInfo[$url]->name.= ' / '.$imageModel->name;
                            }
                            else {
                                $this->imagesWithInfo[$url]->name = $imageModel->name;
                            }
                        }

						if($imageModel->name != null && strlen($imageModel->description) > 0) {
                            if($this->imagesWithInfo[$url]->description != null && strlen($this->imagesWithInfo[$url]->description)>0) {
                                $this->imagesWithInfo[$url]->description.= ' / '.$imageModel->description;
                            }
                            else {
                                $this->imagesWithInfo[$url]->description = $imageModel->description;
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
                $source.= '    <h4>Image from '.$imageWithInfo->source.'</h4>'.PHP_EOL;
                $source.= '    <div class="thumbnail">'.PHP_EOL;
                if($imageWithInfo->name != null && strlen($imageWithInfo->name)>0) {
                    $source.= '      <h5>'.$imageWithInfo->name.'</h5>'.PHP_EOL;
                }
                $source.= '      <img src="'.$imageWithInfo->url.'" /><br />'.PHP_EOL;
                $source.= '      <div class="caption">'.PHP_EOL;
                $source.= '        <table class="table table-condensed">'.PHP_EOL;
                $source.= '          <tr><td valign="top" width="100">Url</td><td><a href="'.$imageWithInfo->url.'" target="_blank">'.$imageWithInfo->url.'</a></td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Width</td><td>'.$imageWithInfo->width.'</td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Height</td><td>'.$imageWithInfo->height.'</td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Mime-Type</td><td>'.$imageWithInfo->mime.'</td></tr>'.PHP_EOL;
                if($imageWithInfo->description != null && strlen($imageWithInfo->description)>0) {
                    $source.= '          <tr><td valign="top">Description</td><td>'.$imageWithInfo->description.'</td></tr>'.PHP_EOL;
                }
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