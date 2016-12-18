<?php
namespace Plugin\HtmlSource;

class HtmlSource extends \Plugin\AbstractPlugin {
    private $shortDescription = '';
    private $longDescription = '';
    
    public function calculate() {
        if(property_exists($this->data, 'ShortDescription')) {
            $this->shortDescription = trim($this->data->ShortDescription);
        }
        if(property_exists($this->data, 'LongDescription')) {
            $this->longDescription = trim($this->data->LongDescription);
        }
    }
    
    public function getResult() {
        return [
            'html' => '<div>'.$this->shortDescription.'</div><div>'.$this->longDescription.'</div>',
        ];
    }
    
    public function getOutput() {
        $source = '';
        if(strlen($this->shortDescription)>0) {
            $source.= '<h4>ShortDescription</h4>'.PHP_EOL;
            $source.= '<div class="row">'.PHP_EOL;
            $source.= '  <div class="col-lg-6">'.PHP_EOL;
            $source.= '    <h5>HTML</h5>'.PHP_EOL;
            $source.= '    <div class="well">'.$this->shortDescription.'</div>'.PHP_EOL;
            $source.= '  </div>'.PHP_EOL;
            $source.= '  <div class="col-lg-6">'.PHP_EOL;
            $source.= '    <h5>Source</h5>'.PHP_EOL;
            $source.= '    <pre class="well">'.htmlentities($this->shortDescription).'</pre>'.PHP_EOL;
            $source.= '  </div>'.PHP_EOL;
            $source.= '</div>'.PHP_EOL;
        }
        if(strlen($this->longDescription)>0) {
            $source.= '<h4>LongDescription</h4>'.PHP_EOL;
            $source.= '<div class="row">'.PHP_EOL;
            $source.= '  <div class="col-lg-6">'.PHP_EOL;
            $source.= '    <h5>HTML</h5>'.PHP_EOL;
            $source.= '    <div class="well">'.$this->longDescription.'</div>'.PHP_EOL;
            $source.= '  </div>'.PHP_EOL;
            $source.= '  <div class="col-lg-6">'.PHP_EOL;
            $source.= '    <h5>Source</h5>'.PHP_EOL;
            $source.= '    <pre class="well">'.htmlentities($this->longDescription).'</pre>'.PHP_EOL;
            $source.= '  </div>'.PHP_EOL;
            $source.= '</div>'.PHP_EOL;
        }
        
        return $source;
    }
}
