<?php

namespace Core;

use Helper\ApiHelper;
use Helper\ConfigHelper;
use Layout\Layout;

class PageRenderer {
    public static function render($page) {
        if(!$page) {
            throw new Exception('No page to render');
        }
        
        $config = ConfigHelper::getConfig();

        switch($page) {
            case Router::PAGE_HOME;
                $layout = new Layout('home', ['config' => $config]);
                $layout->render();
                break;
            case Router::PAGE_ANALYZE;
                $data = ApiHelper::getBaseData();
                $layout = new Layout('analyze', ['layoutConfig' => $config['layout']]);
                PluginRunner::runAllPlugins($layout, $data);
                $layout->render();
                break;
            default:
                throw new Exception('No idea how to render page "'.$page.'"');
        }
    }
}

