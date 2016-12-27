<?php
namespace Plugin\HtmlSource;

class HtmlSource extends \Plugin\AbstractPlugin {
    private $html = [];
    
    public function calculate() {
        foreach($this->parameters['fields'] as $field) {
            if(isset($this->data[$field]) && strlen($this->data[$field])>0) {
                $this->html[$field] = trim($this->data[$field]);
            }
        }
    }
    
    public function getResult() {
        return [
            'html' => '<div>'.implode('</div><div>', $this->html).'</div>',
        ];
    }
    
    public function getOutput() {
        $source = '';
        foreach($this->html as $field => $html)
		{
            if(strlen($html)>0)
			{
                $source.= '<h4>'.$field.'</h4>'.PHP_EOL;
                $source.= '<div class="row">'.PHP_EOL;
                $source.= '  <div class="col-lg-6 limit-img">'.PHP_EOL;
                $source.= '    <h5>HTML</h5>'.PHP_EOL;
                $source.= '    <div class="well">'.$html.'</div>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;
                $source.= '  <div class="col-lg-6">'.PHP_EOL;
                $source.= '    <h5>Source</h5>'.PHP_EOL;
                $source.= '    <pre class="well">'.htmlentities($html).'</pre>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;
                $source.= '</div>'.PHP_EOL;
            }
        }
        
        return $source;
    }
}
