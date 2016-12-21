<?php
namespace Plugin;

abstract class AbstractPlugin implements PluginInterface {
    const PLUGIN_STATUS_FAILED = 0;
    const PLUGIN_STATUS_OK = 1;
    
    protected $data = null;
    protected $parameters = [];
    private $success = false;
    private $status = self::PLUGIN_STATUS_OK;
    
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
