<?php

$env = getenv('APPLICATION_ENV') ?: 'dist';
var_dump($env);
$configfile = 'config/'.$env.'.php';
$config = file_exists($configfile) ? include($configfile) : false;
if(!$config) {
    die('Configfile not found (APPLICATION_ENV='.$env.')');
}
var_dump($config);

