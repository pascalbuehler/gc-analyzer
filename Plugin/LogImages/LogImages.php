<?php
namespace Plugin\LogImages;

use Helper\ConfigHelper;

class LogImages extends \Plugin\AbstractPlugin {
    private $logImages = [];
    public function calculate() {

        $config = ConfigHelper::getConfig();
        $url = $config['apiEndpointImages'].'?'.http_build_query($config['apiParametersImages']);
        $data = file_get_contents($url);
        if(!$data) {
            die('Api not reachable ('.$url.')');
        }
        $data = json_decode($data, true);
                
        foreach ($data['Images'] as $image) {
            
            $imageWithInfoModel = new \Model\ImageWithInfoModel();
            $imageWithInfoModel->url = $image['ThumbUrl'];
            $imageWithInfoModel->description = $image['Name'];

            $extension = pathinfo($imageWithInfoModel->url)['extension'];
            
            if (!($extension == "jpg" || $extension != "jpeg")) {
                continue;
            }
            
            $exif = @exif_read_data($image['Url'], 'FILE', true);
            
            if ($exif != null && $exif != '') {
                if (isset($exif['GPS']))
                {
                    $imageWithInfoModel->exif = $exif;
                    $this->logImages[] = $imageWithInfoModel;
                    $this->setSuccess(true);
                }
            }
        }
    }

    public function getResult() {
        return $this->logImages;
    }
    
    public function getOutput() {
        $source = '';
        if(count($this->logImages)>0)
        {
            foreach($this->logImages as $imageWithInfo)
            {
                $source.= '<div class="row">'.PHP_EOL;
                $source.= '  <div class="col-lg-6 limit-img">'.PHP_EOL;
                $source.= '    <div class="thumbnail">'.PHP_EOL;
                $source.= '      <img src="'.$imageWithInfo->url.'" /><br />'.PHP_EOL;
                $source.= '      <div class="caption">'.PHP_EOL;
                $source.= '        <table class="table table-condensed">'.PHP_EOL;
                $source.= '          <tr><td valign="top" width="100">Url</td><td><a href="'.$imageWithInfo->url.'" target="_blank">'.$imageWithInfo->url.'</a></td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Description</td><td>'.$imageWithInfo->description.'</td></tr>'.PHP_EOL;
                $source.= '        </table>'.PHP_EOL;
                $source.= '      </div>'.PHP_EOL;
                $source.= '    </div>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;
                $source.= '  <div class="col-lg-6">'.PHP_EOL;
                $source.= '    <h4>EXIF</h4>'.PHP_EOL;
                $source.= '    <pre class="pre-scrollable">'.print_r($imageWithInfo->exif, true).'</pre>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;
                $source.= '</div>'.PHP_EOL;
            }
        }

        return $source;
    }
}