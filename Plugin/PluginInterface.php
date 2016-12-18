<?php
namespace Plugin;

interface PluginInterface {
    public function calculate();
    public function getResult();
    public function getOutput();
}
