<?php
namespace Plugin;

use Model\PluginResultModel;

abstract class AbstractPlugin implements PluginInterface {
    protected $data = null;
    protected $parameters = [];
   
    private $success = false;
    private $status = PluginResultModel::PLUGIN_STATUS_OK;
    
    public function __construct(array $data, array $parameters = []) {
        $this->data = $data;
        $this->parameters = $parameters;
    }

    public function getOutput() {
        return '';
    }
    
    public function setSuccess($success) {
        $this->success = $success;
    }
    
    public function getSuccess() {
        return $this->success;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getStatus() {
        return $this->status;
    }
}
