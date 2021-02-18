<?php

use RestAPI\Controller\IndexController;
use RestAPI\Controller\TestController;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions' => false,
        'strategies' => [
            'ViewJsonStrategy',
        ],
        'exception_template' => 'error/index',
        'template_map' => [
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'router' => [
        'routes' => [
            'api' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/api',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'test' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/test[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => TestController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                ]
            ],
        ],
    ],
];
