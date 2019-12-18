<?php
namespace Api;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/v1/:action',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'creategame',
                    ],
                ],
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
        ],
    ],
    /*
    'service_manager' => [
        // Permette di registrare il log a livello di configurazione del modulo
        'abstract_factories' => [
            'Zend\Log\LoggerAbstractServiceFactory',
        ]
    ],
    */
    'view_manager' => [
        
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_path_stack' => [
            'album' => __DIR__ . '/../view',
        ],
        'template_map' => [
            'error/404'    => __DIR__ . '/../view/error/404.phtml',
            'error/index'  => __DIR__ . '/../view/error/index.phtml',
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ]
    ],
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => Driver::class,
                'params' => [
                    'path' => 'data/db.sqlite',
                ],
            ],
        ],
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' =>  __NAMESPACE__ . '_driver',
                ],
            ]
        ]
    ]
];
