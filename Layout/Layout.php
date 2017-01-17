<?php
namespace Layout;

use \Model\PluginResultModel;

class Layout {
    private $template;
    private $templateData = [];
    
    public function __construct($template, array $templateData = []) {
        $this->template = $template;
        $this->templateData = $templateData;
    }
    
    public function addPluginData(PluginResultModel $pluginResult) {
        $this->templateData['plugins'][$pluginResult->name] = [
            'output' => $pluginResult->output,
            'status' => $pluginResult->status,
            'success' => $pluginResult->success,
            'time' => number_format($pluginResult->time, 2),
        ];
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
