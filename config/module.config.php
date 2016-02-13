<?php

namespace ZfMuscle;

/**
 * Description of module.config
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
return array(
    'controllers' => [
        'invokables' => [
            'zfmuscle-system' => 'ZfMuscle\Controller\SystemController',
            'zfmuscle-role' => 'ZfMuscle\Controller\RoleController',
            'zfmuscle-dashboard' => 'ZfMuscle\Controller\DashboardController',
        ]
    ],
    'router' => [
        'routes' => [
            'zfmuscle' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/zfmuscle',
                    'defaults' => [
                        'controller'    => 'zfmuscle-dashboard',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'install' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/install[/]',
                            'defaults' => [
                                'controller' => 'zfmuscle',
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'dashboard' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/dashboard',
                            'defaults' => [
                                'controller' => 'zfmuscle-dashboard',
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'system' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/system/config',
                            'defaults' => [
                                'controller' => 'zfmuscle-system',
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'resource' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/resources/update',
                                    'defaults' => [
                                        'controller' => 'zfmuscle-system',
                                        'action'     => 'update-resource',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'users' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/users',
//                            'defaults' => array(
//                                'controller' => 'zfmuscle-user',
//                                'action'     => 'index',
//                            ),
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'login' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/login',
                                    'defaults' => [
                                        'controller' => 'zfmuscle-user',
                                        'action'     => 'login',
                                    ],
                                ],
                            ],
                            'logout' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/logout',
                                    'defaults' => [
                                        'controller' => 'zfmuscle-user',
                                        'action'     => 'logout',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'permission' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/permission',
//                            'defaults' => array(
//                                'controller' => 'zfmuscle-user',
//                                'action'     => 'index',
//                            ),
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'users' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/users',
                                    'defaults' => [
                                        'controller' => 'zfmuscle-user',
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'index' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/[page/:page]',
                                            'constraints' => [
                                                'page' => '[0-9]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/view[/:id]',
                                            'constraints' => [
                                                'id' => '[0-9]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'register',
                                            ],
                                        ],
                                    ],
                                    'add' => [
                                        'type' => 'Literal',
                                        'options' => [
                                            'route' => '/add',
                                            'defaults' => [
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'register',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/edit[/:id]',
                                            'constraints' => [
                                                'id' => '[0-9]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'register',
                                            ],
                                        ],
                                    ],
                                    'delete' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/delete[/:id]',
                                            'constraints' => [
                                                'id' => '[0-9]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'delete',
                                            ],
                                        ],
                                    ],
                                    'logout' => [
                                        'type' => 'Literal',
                                        'options' => [
                                            'route' => '/logout',
                                            'defaults' => [
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'logout',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'roles' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/roles',
                                    'defaults' => [
                                        'controller' => 'zfmuscle-role',
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'index' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/[page/:page]',
                                            'constraints' => [
                                                'page' => '[0-9]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'zfmuscle-role',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'add' => [
                                        'type' => 'Literal',
                                        'options' => [
                                            'route' => '/add',
                                            'defaults' => [
                                                'controller' => 'zfmuscle-role',
                                                'action'     => 'entry',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/edit[/:id]',
                                            'constraints' => [
                                                'id' => '[0-9]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'zfmuscle-role',
                                                'action'     => 'entry',
                                            ],
                                        ],
                                    ],
                                    'delete' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/delete[/:id]',
                                            'constraints' => [
                                                'id' => '[0-9]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'zfmuscle-role',
                                                'action'     => 'delete',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories' => [
            'navigation'                    => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'ZfMuscle\Config'               => 'ZfMuscle\Service\ConfigServiceFactory',
            'ZfMuscle\App\Config'           => 'ZfMuscle\Service\ApplicationServiceFactory',
            'ZfMuscle\Service\RoleResource' => 'ZfMuscle\Service\Factory\RoleResourceFactory',
            'BjyAuthorize\Guard\Route'      => 'ZfMuscle\Service\Factory\ApplicationGuardServiceFactory',
        ],
        'invokables' => [
//            'BjyAuthorize\View\RedirectionStrategy' => 'ZfMuscle\View\RedirectionStrategy',
//            'ZfMuscle\Event\Listener\Application' => 'ZfMuscle\Event\Listener\Application',
//            'ZfMuscle\Event\Listener\UserListener' => 'ZfMuscle\Event\Listener\UserListener',
//            'ZfMuscle\Event\Listener\RoleListener' => 'ZfMuscle\Event\Listener\RoleListener',
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Dashboard',
                'route' => 'zfmuscle/dashboard',
                'thumbnail' => 'glyphicon-home'
            ],
            [
                'label' => 'System',
                'uri' => '#',
                'route' => 'zfmuscle/system',
                'thumbnail' => 'glyphicon-globe',
                'pages' => [
                    [
                        'label' => 'Config',
                        'route' => 'zfmuscle/system',
                        'thumbnail' => 'glyphicon-tasks',
                    ],
                ],
            ],
            [
                'label' => 'Permission',
                'uri' => '#',
                'route' => 'zfmuscle/permission/users,zfmuscle/permission/roles',
                'thumbnail' => 'glyphicon-open-file',
                'pages' => [
                    [
                        'label' => 'Users',
                        'route' => 'zfmuscle/permission/users',
                        'thumbnail' => 'glyphicon-user',
                    ],
                    [
                        'label' => 'Roles',
                        'route' => 'zfmuscle/permission/roles',
                        'thumbnail' => 'glyphicon-signal',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'zfmuscle' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
        'display_exceptions' => true,
    ],
    'view_helpers' => [
        'invokables'=> [
//            'RoleResourceHelper' => 'ZfMuscle\Core\View\Helper\RoleResourceHelper',
        ]
    ],
//    'asset_manager' => array(
//        'resolver_configs' => array(
//            'collections' => array(
//                'css/all.css' => array(
//                    'zf-muscle/css/bootstrap/bootstrap.min.css',
//                    'zf-muscle/css/zf-muscle.css',
//                    'zf-muscle/css/reset.css',
//                ),
//                'js/all.js' => array(
//                    'zf-muscle/js/jquery/jquery-1.11.1.min.js',
//                    'zf-muscle/js/bootstrap/bootstrap.min.js',
//                    'zf-muscle/js/jquery-validation/js/jquery.validate.min.js',
//                    'zf-muscle/js/jquery-validation/js/additional-methods.min.js',
//                ),
//            ),
//            'paths' => array(
//                __DIR__ . '/../public',
//            ),
//        ),
//    ),
    
    'doctrine' => [
        'driver' => [
            'zfmuscle_entity' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => __DIR__ . '/../src/ZfMuscle/Entity',
            ],
 
            'orm_default' => [
                'drivers' => [
                    'ZfMuscle\Entity' => 'zfmuscle_entity',
                ],
            ],
        ]
    ],
    'zfcuser' => [
        // telling ZfcUser to use our own class
        'user_entity_class' => 'ZfMuscle\Entity\User',
        // telling ZfcUserDoctrineORM to skip the entities it defines
        'enable_default_entities' => false,
    ],
);