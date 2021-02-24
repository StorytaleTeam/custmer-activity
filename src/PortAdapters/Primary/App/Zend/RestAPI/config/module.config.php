<?php

use RestAPI\Controller\IndexController;
use RestAPI\Controller\SubscriptionPlanController;
use RestAPI\Controller\SubscriptionPlanCustomerController;
use RestAPI\Controller\CustomerController;
use RestAPI\Controller\SubscriptionController;
use RestAPI\Controller\SubscriptionCustomerController;
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
                    'customer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/customer[/:id]',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => CustomerController::class,
                                'action' => null,
                            ],
                        ],
                    ],
                    'subscriptionPlan' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/subscriptionPlan[/:id]',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => SubscriptionPlanController::class,
                                'action' => null,
                            ],
                        ],
                    ],
                    'customerSubscriptionPlan' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/customer/subscriptionPlan[/:id]',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => SubscriptionPlanCustomerController::class,
                                'action' => null,
                            ],
                        ],
                    ],
                    'subscription' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/subscription[/:id]',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => SubscriptionController::class,
                                'action' => null,
                            ],
                        ],
                    ],
                    'customerSubscription' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/customer/subscription[/:id]',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => SubscriptionCustomerController::class,
                                'action' => null,
                            ],
                        ],
                    ],
                ]
            ],
        ],
    ],
];
