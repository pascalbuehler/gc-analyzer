<?php
namespace Plugin\LogImages;

use Helper\ConfigHelper;
use Helper\CoordsHelper;

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
            $logImageModel = new \Model\LogImageModel();
            $logImageModel->url = $image['Url'];
            $logImageModel->thumbUrl = $image['ThumbUrl'];
            $logImageModel->description = $image['Name'];

            $extension = pathinfo($logImageModel->url)['extension'];
            
            if (!($extension == "jpg" || $extension != "jpeg")) {
                continue;
            }
            
            $exif = @exif_read_data($logImageModel->url, 0, true);
            $logImageModel->exif = $exif;
            
            if ($exif == null || $exif == '') {
                continue;
            }
            if (!isset($exif['GPS'])) {
                continue;
            }
            $pos = $this->getGpsPositionFromExif($exif);
            if ($pos) {
                $logImageModel->latitude = $pos[0];
                $logImageModel->longitude = $pos[1];
            }
            
            $this->logImages[] = $logImageModel;
            $this->setSuccess(true);
            
            // 301: store corrected location (for Jeffreys)
            $headers = get_headers($logImageModel->url, 1);
            $logImageModel->url = $headers['Location'];
        }
    }
    
    private function getGpsPositionFromExif($exif) {
        $lat = isset($exif['GPS']['GPSLatitude']) ? $exif['GPS']['GPSLatitude'] : false; 
        $lng = isset($exif['GPS']['GPSLongitude']) ? $exif['GPS']['GPSLongitude'] : false;
        if (!$lat || !$lng) {
            return null;
        }
        
        // latitude values //
        $lat_degrees = $this->divide($lat[0]);
        $lat_minutes = $this->divide($lat[1]);
        $lat_seconds = $this->divide($lat[2]);
        $lat_hemi = $exif['GPS']['GPSLatitudeRef'];

        // longitude values //
        $log_degrees = $this->divide($lng[0]);
        $log_minutes = $this->divide($lng[1]);
        $log_seconds = $this->divide($lng[2]);
        $log_hemi = $exif['GPS']['GPSLongitudeRef'];

        $lat_decimal = $this->toDecimal($lat_degrees, $lat_minutes, $lat_seconds, $lat_hemi);
        $log_decimal = $this->toDecimal($log_degrees, $log_minutes, $log_seconds, $log_hemi);

        return array($lat_decimal, $log_decimal);
    }

    private function toDecimal($deg, $min, $sec, $hemi)
    {
        $d = $deg + $min/60 + $sec/3600;
        return ($hemi=='S' || $hemi=='W') ? $d*=-1 : $d;
    }

    private function divide($a)
    {
        // evaluate the string fraction and return a float
        $e = explode('/', $a);
        // prevent division by zero
        if (!$e[0] || !$e[1]) {
            return 0;
        } else {
            return $e[0] / $e[1];
        }
    }

    public function getResult() {
        return $this->logImages;
    }
    
    public function getOutput() {
        $source = '';
        if(count($this->logImages)>0)
        {
            foreach($this->logImages as $logImage)
            {
                $hasCoords = $logImage->latitude!==null && $logImage->longitude!==null;
                if($hasCoords) {
                    $coords = CoordsHelper::convertDecimalToDecimalMinute($logImage->latitude, $logImage->longitude);
                }
                
                $source.= '<div class="row">'.PHP_EOL;
                $source.= '  <div class="col-lg-6 limit-img">'.PHP_EOL;
                $source.= '    <div class="thumbnail">'.PHP_EOL;
                $source.= '      <img src="'.$logImage->thumbUrl.'" /><br />'.PHP_EOL;
                $source.= '      <div class="caption">'.PHP_EOL;
                $source.= '        <table class="table table-condensed">'.PHP_EOL;
                $source.= '          <tr><td valign="top" width="100">Url</td><td><a href="'.$logImage->url.'" target="_blank">'.$logImage->url.'</a></td></tr>'.PHP_EOL;
                $source.= '          <tr><td valign="top">Description</td><td>'.$logImage->description.'</td></tr>'.PHP_EOL;
                if($hasCoords) {
                    $source.= '          <tr><td valign="top">Position</td><td>'.$coords.'</td></tr>'.PHP_EOL;
                }
                $source.= '          <tr><td valign="top" colspan="2"><a href="http://exif.regex.info/exif.cgi?imgurl='.$logImage->url.'" target="_blank"><span class="glyphicon glyphicon-search"></span> Jeffreys (exif)</a></td></tr>'.PHP_EOL;
                $source.= '        </table>'.PHP_EOL;
                $source.= '      </div>'.PHP_EOL;
                $source.= '    </div>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;
                $source.= '  <div class="col-lg-6">'.PHP_EOL;
                $source.= '    <h4>EXIF</h4>'.PHP_EOL;
                $source.= '    <pre class="pre-scrollable">'.print_r($logImage->exif, true).'</pre>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;
                $source.= '</div>'.PHP_EOL;
            }
        }

        return $source;
    }
}