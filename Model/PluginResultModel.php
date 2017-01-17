<?php
namespace Model;

use Plugin\AbstractPlugin;

class PluginResultModel {
    public $name = '';
    public $result = [];
    public $output = '';
    public $time = 0;
    public $status = AbstractPlugin::PLUGIN_STATUS_OK;
    public $success = false;
}