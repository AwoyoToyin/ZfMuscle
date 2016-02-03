<?php

namespace ZfMuscle;

/**
 * Description of module.config
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
return array(
    'controllers' => array(
        'invokables' => array(
            'zfmuscle-system' => 'ZfMuscle\Controller\SystemController',
            'zfmuscle-role' => 'ZfMuscle\Controller\RoleController',
            'zfmuscle-dashboard' => 'ZfMuscle\Controller\DashboardController',
        )
    ),
    'router' => array(
        'routes' => array(
            'zfmuscle' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/zfmuscle',
                    'defaults' => array(
                        'controller'    => 'zfmuscle-dashboard',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'install' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/install[/]',
                            'defaults' => array(
                                'controller' => 'zfmuscle',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'dashboard' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/dashboard',
                            'defaults' => array(
                                'controller' => 'zfmuscle-dashboard',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'system' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/system/config',
                            'defaults' => array(
                                'controller' => 'zfmuscle-system',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'resource' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/resources/update',
                                    'defaults' => array(
                                        'controller' => 'zfmuscle-system',
                                        'action'     => 'update-resource',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'users' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/users',
//                            'defaults' => array(
//                                'controller' => 'zfmuscle-user',
//                                'action'     => 'index',
//                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'login' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/login',
                                    'defaults' => array(
                                        'controller' => 'zfmuscle-user',
                                        'action'     => 'login',
                                    ),
                                ),
                            ),
                            'logout' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/logout',
                                    'defaults' => array(
                                        'controller' => 'zfmuscle-user',
                                        'action'     => 'logout',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'permission' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/permission',
//                            'defaults' => array(
//                                'controller' => 'zfmuscle-user',
//                                'action'     => 'index',
//                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'users' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/users',
                                    'defaults' => array(
                                        'controller' => 'zfmuscle-user',
                                        'action'     => 'index',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'index' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/[page/:page]',
                                            'constraints' => array(
                                                'page' => '[0-9]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'index',
                                            ),
                                        ),
                                    ),
                                    'view' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/view[/:id]',
                                            'constraints' => array(
                                                'id' => '[0-9]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'register',
                                            ),
                                        ),
                                    ),
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'register',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit[/:id]',
                                            'constraints' => array(
                                                'id' => '[0-9]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'register',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete[/:id]',
                                            'constraints' => array(
                                                'id' => '[0-9]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'delete',
                                            ),
                                        ),
                                    ),
                                    'logout' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/logout',
                                            'defaults' => array(
                                                'controller' => 'zfmuscle-user',
                                                'action'     => 'logout',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'roles' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/roles',
                                    'defaults' => array(
                                        'controller' => 'zfmuscle-role',
                                        'action'     => 'index',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'index' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/[page/:page]',
                                            'constraints' => array(
                                                'page' => '[0-9]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'zfmuscle-role',
                                                'action'     => 'index',
                                            ),
                                        ),
                                    ),
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'controller' => 'zfmuscle-role',
                                                'action'     => 'entry',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit[/:id]',
                                            'constraints' => array(
                                                'id' => '[0-9]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'zfmuscle-role',
                                                'action'     => 'entry',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete[/:id]',
                                            'constraints' => array(
                                                'id' => '[0-9]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'zfmuscle-role',
                                                'action'     => 'delete',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'navigation'                => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'ZfMuscle\Config'           => 'ZfMuscle\Service\ConfigServiceFactory',
            'ZfMuscle\App\Config'       => 'ZfMuscle\Service\ApplicationServiceFactory',
            'BjyAuthorize\Guard\Route'  => 'ZfMuscle\Service\ApplicationGuardServiceFactory',
        ),
        'invokables' => array(
//            'ZfMuscle\Event\Listener\Application' => 'ZfMuscle\Event\Listener\Application',
//            'ZfMuscle\Event\Listener\UserListener' => 'ZfMuscle\Event\Listener\UserListener',
//            'ZfMuscle\Event\Listener\RoleListener' => 'ZfMuscle\Event\Listener\RoleListener',
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Dashboard',
                'route' => 'zfmuscle/dashboard',
                'controller' => 'zfmuscle-dashboard',
                'thumbnail' => 'pg-home'
            ),
            array(
                'label' => 'System',
                'uri' => '#',
                'controller' => 'zfmuscle-system',
                'thumbnail' => 'Sm',
                'thumbnail_not_class' => true,
                'pages' => array(
                    array(
                        'label' => 'Config',
                        'route' => 'zfmuscle/system',
                        'controller' => 'zfmuscle-system',
                        'thumbnail' => 'Cf',
                        'thumbnail_not_class' => true,
                    ),
                ),
            ),
            array(
                'label' => 'Permission',
                'uri' => '#',
                'controller' => 'zfmuscle-user,zfmuscle-role',
                'thumbnail' => 'Ps',
                'thumbnail_not_class' => true,
                'pages' => array(
                    array(
                        'label' => 'Users',
                        'route' => 'zfmuscle/permission/users',
                        'controller' => 'zfmuscle-user',
                        'thumbnail' => 'Us',
                        'thumbnail_not_class' => true,
                    ),
                    array(
                        'label' => 'Roles',
                        'route' => 'zfmuscle/permission/roles',
                        'controller' => 'zfmuscle-role',
                        'thumbnail' => 'Rs',
                        'thumbnail_not_class' => true,
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/403' => __DIR__ . '/../view/error/403.phtml',
        ),
        'template_path_stack' => array(
            'zfmuscle' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'display_exceptions' => true,
    ),
    'view_helpers' => array(
        'invokables'=> array(
//            'RoleResourceHelper' => 'ZfMuscle\Core\View\Helper\RoleResourceHelper',
        )
    ),
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
    
    'doctrine' => array(
        'driver' => array(
            'zfmuscle_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => __DIR__ . '/../src/ZfMuscle/Entity',
            ),
 
            'orm_default' => array(
                'drivers' => array(
                    'ZfMuscle\Entity' => 'zfmuscle_entity',
                ),
            ),
        )
    ),
    'zfcuser' => array(
        // telling ZfcUser to use our own class
        'user_entity_class' => 'ZfMuscle\Entity\User',
        // telling ZfcUserDoctrineORM to skip the entities it defines
        'enable_default_entities' => false,
    ),
);