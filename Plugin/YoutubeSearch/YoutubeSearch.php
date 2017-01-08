<?php
namespace Plugin\YoutubeSearch;

use Helper\ConfigHelper;

class YoutubeSearch extends \Plugin\AbstractPlugin {
    private $googleApiToken = false;
    private $youtubeResults = [];

    public function calculate() {
        $config = ConfigHelper::getConfig();
        $this->googleApiToken = isset($config['googleApiToken']) ? $config['googleApiToken'] : false;
        if(!$this->googleApiToken) {
            return;
        }
        
        foreach($this->parameters['fields'] as $field) {
            $query = $this->data[$field];
            
            $url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&q='.$query.'&key='.$config['googleApiToken'];
            $body = file_get_contents($url);
            $json = json_decode($body);

            foreach ($json->items as $item) {
                $youtubeResultModel = new \Model\YoutubeResultModel();
                
                $youtubeResultModel->videoId = $item->id->videoId;
                $youtubeResultModel->title = $item->snippet->title;
                $youtubeResultModel->channel = $item->snippet->channelTitle;
                $youtubeResultModel->description = $item->snippet->description;
                $youtubeResultModel->thumbnail = $item->snippet->thumbnails->medium->url;
                
                $this->youtubeResults[$field][] = $youtubeResultModel;
                $this->setSuccess(true);
            }
        }
    }
    
    public function getResult() {
        return $this->youtubeResults;
    }

    public function getOutput() {
        if(!$this->googleApiToken) {
            $source = 'googleApiToken not configured: search not possible';
            return $source;
        }
        
        $source = '';
        foreach($this->youtubeResults as $results) {
            foreach($results as $result) {
                $source.= '<div class="row">'.PHP_EOL;
                $source.= '  <div class="col-lg-6 limit-img">'.PHP_EOL;
                $source.= '    <div class="thumbnail">'.PHP_EOL;                
                $source.= '      <h5>'.$result->channel.': '.$result->title.'</h5>'.PHP_EOL;
                $source.= '      <a href="https://www.youtube.com/watch?v='.$result->videoId.'" target="_blank"><img src="'.$result->thumbnail.'" /></a><br />'.PHP_EOL;
                $source.= '      <p><a href="https://www.youtube.com/watch?v='.$result->videoId.'" target="_blank">https://www.youtube.com/watch?v='.$result->videoId.'</a></p>'.PHP_EOL;
                $source.= '      <p>'.$result->description.'</p>'.PHP_EOL;
                $source.= '    </div>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;
                $source.= '</div>'.PHP_EOL;
            }
        }
        return $source;
    }
}