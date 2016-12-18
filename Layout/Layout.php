<?php
namespace Layout;

class Layout {
    private $data = null;
    private $pluginData = [];
    
    public function __construct(array $data) {
        $this->data = $data;
    }
    
    public function addPluginData($pluginName, $pluginOutput, $pluginSuccess) {
        $this->pluginData[$pluginName] = [
            'output' => $pluginOutput,
            'success' => $pluginSuccess
        ];
    }
    
    public function render() {
        $data = $this->data;
        $pluginData = $this->pluginData;
        include('view/layout.phtml');
    }
}
