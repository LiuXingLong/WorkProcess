<?php
return [
    'api' => [
        'index/index' => [
            'method' => 'GET',
            'controller' => 'IndexController',
            'action' => 'index',
        ],
    ],
    'backend' => [
        'index/index' => [
            'method' => 'GET',
            'controller' => 'IndexController',
            'action' => 'index',
        ],
    ], 
    'tasks' => [
        'index@index' => [
            'task' => 'IndexTask',
            'action' => 'index',
        ],
    ],
];