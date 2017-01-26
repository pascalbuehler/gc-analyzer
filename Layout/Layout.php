<?php
namespace Layout;

use Helper\ArrayHelper;

class Layout {
    private $template;
    private $templateData = [];
    
    public function __construct($template, array $templateData = []) {
        $this->template = $template;
        $this->templateData = $templateData;
    }
    
    public function addData($name, $value) {
        $this->templateData = ArrayHelper::insertNestedValue($this->templateData, $name, $value);
    }

    public function render() {
        // Export plugin data
        foreach($this->templateData as $dataName => $data) {
            $$dataName = $data;
        }
        
        $templateName = 'view/'.$this->template.'.phtml';
        include($templateName);
    }
}
