<?php
namespace Layout;

class Layout {
    private $template;
    private $templateData = [];
    
    public function __construct($template, array $templateData = []) {
        $this->template = $template;
        $this->templateData = $templateData;
    }
    
    public function addPluginData($pluginName, $pluginOutput, $pluginStatus, $pluginSuccess) {
        $this->templateData['plugins'][$pluginName] = [
            'output' => $pluginOutput,
            'status' => $pluginStatus,
            'success' => $pluginSuccess
        ];
    }
    
    public function render() {
        // Export template data
        foreach($this->templateData as $dataName => $data) {
            $$dataName = $data;
        }
        
        $templateName = 'view/'.$this->template.'.phtml';
        include($templateName);
    }
}
