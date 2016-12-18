## Introduction
This project provides a basic framework for analyzing geocache listings.

An external API for retrieving the listing from geocaching.com is needed to run.

## Installation
1. Download or clone the project
2. Set up a virtual host
3. Add environment variable to the virtual host (Apache: `SetEnv APPLICATION_ENV dev`)
4. Create a configuration for your environment in `Config/` (copy `dist.php` and add apiEndpoint + token)
5. Run by calling `http://<project-root>/?code=GC40`

## Writing a plugin
1. Create a plugin folder in `Plugin` (e.g. `MyPlugin`)
2. Add your plugin code to `Plugin/MyPlugin.php` (see example below)
3. Register your Plugin in the config

Example plugin config:
```php
    'plugins' => [
        'MyPlugin' => [
            'class' => Plugin\MyPlugin\MyPlugin::class,
            'parameters' => [
                'quiet' => false,
            ],
        ],
    ],
```

Example plugin code:
```php
<?php
namespace Plugin\MyPlugin;

class MyPlugin extends \Plugin\AbstractPlugin {
    protected $quiet = false;
    protected $owner = false;
    
    public function calculate() {
        // Get a parameter for this plugin
        $this->quiet = $this->parameters['quiet'];
        // Grab some data
        $this->owner = $this->data['Owner'];
        // Set the success state
        $this->setSuccess($this->owner=='frigidor');
    }
    
    public function getResult() {
        return $this->owner;
    }
    
    public function getOutput() {
        $source = false;
        if(!$this->quiet) {
            $source = '<div>'.$this->owner.'</div>';
        }
        return $source;
    }
}
?>
```