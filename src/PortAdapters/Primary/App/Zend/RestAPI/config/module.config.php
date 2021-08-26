<?php

use RestAPI\Controller\IndexController;
use RestAPI\Controller\SubscriptionPlanController;
use RestAPI\Controller\SubscriptionPlanCustomerController;
use RestAPI\Controller\CustomerController;
use RestAPI\Controller\SubscriptionController;
use RestAPI\Controller\SubscriptionCustomerController;
use RestAPI\Controller\DownloadController;
use RestAPI\Controller\DownloadCustomerController;
use RestAPI\Controller\LikeCustomerController;
use RestAPI\Controller\IllustrationController;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use RestAPI\Controller\OrderController;
use RestAPI\Controller\OrderCustomerController;
use RestAPI\Controller\NewsletterController;

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
                    'order' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/order[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => OrderController::class,
                                'action' => null,
                            ],
                        ],
                    ],
                    'orderCustomer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/customer/order[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => OrderCustomerController::class,
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
                    'download' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/download[/:id]',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => DownloadController::class,
                                'action' => null,
                            ],
                        ],
                    ],
                    'customerDownload' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/customer/download[/:id]',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => DownloadCustomerController::class,
                                'action' => null,
                            ],
                        ],
                    ],
                    'customerLike' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/customer/like[/:id]',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => LikeCustomerController::class,
                                'action' => null,
                            ],
                        ],
                    ],
                    'illustration' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/illustration/:action',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => IllustrationController::class,
                                'action' => null,
                            ],
                        ],
                    ],
                    'newsletter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/newsletter/:action',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => NewsletterController::class,
                                'action' => null,
                            ],
                        ],
                    ]
                ]
            ],
        ],
    ],
];
