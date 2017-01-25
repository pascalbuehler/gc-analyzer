<?php
namespace Model;

class PluginResultModel {
    const PLUGIN_STATUS_FAILED = 0;
    const PLUGIN_STATUS_OK = 1;

    public $name = '';
    public $result = [];
    public $output = '';
    public $time = 0;
    public $status = self::PLUGIN_STATUS_OK;
    public $success = false;
    public $runMode = false;
}