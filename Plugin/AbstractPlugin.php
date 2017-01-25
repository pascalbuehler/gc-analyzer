<?php
namespace Plugin;

use Core\PluginRunner;
use Model\PluginResultModel;

abstract class AbstractPlugin implements PluginInterface {
    protected $data = null;
    protected $parameters = [];
   
    private $runMode = false;
    private $success = false;
    private $status = PluginResultModel::PLUGIN_STATUS_OK;
    
    public function __construct(array $data, array $parameters = [], $runMode = PluginRunner::RUN_PLUGIN_SYNC) {
        $this->data = $data;
        $this->parameters = $parameters;
        $this->runMode = $runMode;
    }

    public function getOutput() {
        return '';
    }
    
    public function setRunMode($runMode) {
        $this->runMode = $runMode;
    }
    
    public function getRunMode() {
        return $this->runMode;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getStatus() {
        return $this->status;
    }

    public function setSuccess($success) {
        $this->success = $success;
    }
    
    public function getSuccess() {
        return $this->success;
    }
}
