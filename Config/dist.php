<?php

use Core\InputParameters;

return [
    'apiEndpoint' => '',
    'apiParameters' => [
        'token' => '',
        'code' => InputParameters::getParameter('code'),
    ],
    
    'apiEndpointImages' => '',
    'apiParametersImages' => [
        'token' => '',
        'code' => InputParameters::getParameter('code'),
    ],
    
    'plugins' => include('plugins.php'),
    
    'layout' => [
        'printPluginRuntime' => false,
    ],

    'googleApiToken' => '',
];