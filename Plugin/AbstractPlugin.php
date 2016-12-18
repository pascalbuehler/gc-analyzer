<?php
namespace Plugin;

abstract class AbstractPlugin implements PluginInterface {
    protected $data = null;
    protected $parameters = [];
    private $success = false;
    
    public function __construct(array $data, array $parameters = []) {
        $this->data = $data;
        $this->parameters = $parameters;
    }

    public function getOutput() {
        return false;
    }
    
    public function setSuccess($success) {
        $this->success = $success;
    }
    
    public function getSuccess() {
        return $this->success;
    }
}
