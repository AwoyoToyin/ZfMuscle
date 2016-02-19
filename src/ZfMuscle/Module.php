<?php

namespace ZfMuscle;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Helper\FlashMessenger;
use ZfcUser\Form\LoginFilter;
use ZfcUser\Form\RegisterFilter;
use ZfcUser\Validator\NoRecordExists;
use ZfMuscle\Form\Login;
use ZfMuscle\Form\Role;
use ZfMuscle\Form\User;
use ZfMuscle\Options\ModuleOptions;
use ZfMuscle\Provider\ResourceProvider;
use ZfMuscle\Provider\RoleProvider;
use ZfMuscle\Provider\UserProvider;
use ZfMuscle\Service\ApplicationService;
use ZfMuscle\Service\ResourceService;
use ZfMuscle\Service\RoleService;
use ZfMuscle\Service\UserService;
use ZfMuscle\Session\Storage\AuthSessionStorage;
use ZfMuscle\View\Helper\RoleHelper;
use ZfMuscle\View\Helper\RoleResourceHelper;

/**
 * Description of Module
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class Module
{
    public function onBootstrap(MvcEvent $event)
    {
        $app            = $event->getTarget();
        $eventManager   = $app->getEventManager();
        $sm             = $app->getServiceManager();
        $sharedManager  = $eventManager->getSharedManager(); // needed to extend zfcuser form and custom events listeners

        $appConfig      = $sm->get('ZfMuscle\App\Config');  // get application.config
        foreach ($appConfig['listeners'] as $key => $listener)
        {
            $eventManager->attach($sm->get($listener));
        }


        // update user form with more fields
        $zfcServiceEvents = $this->updateUserForm($sm, $sharedManager);

        // Store the field
        $this->storeFormUpdateValue($sm, $zfcServiceEvents);


        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Show flash messages in the view
        $this->attachFlashMessenger($eventManager);
    }

    /**
     * @param $sm
     * @param $sharedManager
     * @return mixed
     */
    public function updateUserForm($sm, $sharedManager)
    {
        $zfcServiceEvents = $sm->get('zfcuser_user_service')->getEventManager();
        // To validate new field
        $sharedManager->attach('ZfcUser\Form\RegisterFilter', 'init', function ($e) {
            $filter = $e->getTarget();
            $filter->add([
                'name' => 'firstname',
                'required' => true,
                'allowEmpty' => false,
                'filters' => [['name' => 'StringTrim']],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                    ]
                ],
            ]);
            $filter->add([
                'name' => 'lastname',
                'required' => true,
                'allowEmpty' => false,
                'filters' => [['name' => 'StringTrim']],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                    ]
                ],
            ]);
        });
        return $zfcServiceEvents;
    }

    /**
     * @param $sm
     * @param $zfcServiceEvents
     */
    public function storeFormUpdateValue($sm, $zfcServiceEvents)
    {
        $entityManager = $sm->get('doctrine.entitymanager.orm_default');

        $zfcServiceEvents->attach('register', function ($e) use ($entityManager) {
            $form = $e->getParam('form');
            $user = $e->getParam('user');

            /* @var $user \ZfMuscle\Entity\User */
            $user->setUsername($form->get('username')->getValue());
            $user->setFirstname($form->get('firstname')->getValue());

            // if user role field exists
            if ($form->get('role')->getValue()) {
                $role = $entityManager->getReference('ZfMuscle\Entity\Role', $form->get('role')->getValue());
                $user->addRole($role);
            }
        });
    }

    /**
     * @param $eventManager
     */
    public function attachFlashMessenger($eventManager)
    {
        $eventManager->attach(MvcEvent::EVENT_RENDER, function ($e) {
            $flashMessenger = new FlashMessenger;

            $messages = [];

            $flashMessenger->setNamespace('success');
            if ($flashMessenger->hasMessages()) {
                $messages['success'] = $flashMessenger->getMessages();
            }
            $flashMessenger->clearMessages();

            $flashMessenger->setNamespace('warning');
            if ($flashMessenger->hasMessages()) {
                $messages['warning'] = $flashMessenger->getMessages();
            }
            $flashMessenger->clearMessages();

            $flashMessenger->setNamespace('danger');
            if ($flashMessenger->hasMessages()) {
                $messages['danger'] = $flashMessenger->getMessages();
            }
            $flashMessenger->clearMessages();

            $e->getViewModel()->setVariable('flashMessages', $messages);
        });
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'RoleResourcePlugin' => 'ZfMuscle\Controller\Plugin\Factory\RoleResourcePluginFactory',
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                'zfmuscle' => 'ZfMuscle\Factory\Controller\InstallControllerFactory',
                'zfmuscle-user' => 'ZfMuscle\Factory\Controller\UserControllerFactory',
            ],
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'RoleResourceHelper' => function ($sm) {
                    $helper = new RoleResourceHelper();
                    $helper->setServiceLocator($sm);
                    return $helper;
                },
                'RoleHelper' => function ($sm) {
                    $helper = new RoleHelper();
                    $helper->setServiceLocator($sm);
                    return $helper;
                }
            ],
        ];
    }
    
    public function getServiceConfig()
    {
        return [
            'invokables' => [

            ],
            'factories' => [
                'Zend\Authentication\AuthenticationService' => function($sm) {
                    return $sm->get('doctrine.authenticationservice.orm_default');
                },
                'zfcuser_module_options' => function ($sm) {
                    $config = $sm->get('Configuration');
                    return new ModuleOptions(isset($config['zfcuser']) ? $config['zfcuser'] : []);
                },
                'zfmuscle_user_service' => function ($sm) {
                    $service = new UserService();
                    $service->setServiceManager($sm);
                    $provider = new UserProvider();
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $provider->setEntityManager($em);
                    $service->setProvider($provider);
                    return $service;
                },
                'zfmuscle_role_service' => function ($sm) {
                    $service = new RoleService();
                    $service->setServiceManager($sm);
                    $formHydrator = $sm->get('default_hydrator');
                    $service->setFormHydrator($formHydrator);
                    $provider = new RoleProvider();
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $provider->setEntityManager($em);
                    $service->setProvider($provider);
                    return $service;
                },
                'zfmuscle_resource_service' => function ($sm) {
                    $service = new ResourceService();
                    $service->setServiceManager($sm);
                    $provider = new ResourceProvider();
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $provider->setEntityManager($em);
                    $service->setProvider($provider);
                    return $service;
                },
                'zfmuscle_application_service' => function ($sm) {
                    $service = new ApplicationService();
                    $service->setServiceManager($sm);
                    $service->setXmlInstallPath("config".DIRECTORY_SEPARATOR."local.xml");
                    return $service;
                },
                'zfcuser_login_form' => function ($sm) {
                    $options = $sm->get('zfcuser_module_options');
                    $form = new Login(null, $options);
                    $form->setInputFilter(new LoginFilter($options));
                    return $form;
                },
                'zfcuser_register_form' => function($sm){
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $options = $sm->get('zfcuser_module_options');
                    $form = new User($em, null, $options);
//                    $form->setCaptchaElement($sm->get('zfcuser_captcha_element'));
                    $form->setInputFilter(new RegisterFilter(
                        new NoRecordExists([
                            'mapper' => $sm->get('zfcuser_user_mapper'),
                            'key'    => 'email'
                        ]),
                        new NoRecordExists([
                            'mapper' => $sm->get('zfcuser_user_mapper'),
                            'key'    => 'username'
                        ]),
                        $options
                    ));
                    return $form;
                },
                'zfmuscle_role_form' => function ($sm) {
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $form = new Role($em);
//                    $form->setServiceManager($sm);
                    return $form;
                },
                'AuthSessionStorage' => function($sm) {
                    $config = $sm->get('config');
                    $authSessionStorage = new AuthSessionStorage(
                        'zfMuscle',
                        null,
                        $sm->get('Zend\Session\SessionManager')
                    );
                    $authSessionStorage->setAllowedIdleTimeInSeconds($config['session']['config']['authentication_expiration_time']);
                    return $authSessionStorage;
                },
//                'AuthService' => function($sm) {
//                    $authAdapter = new AuthAdapter();
//                    $authAdapter->prepareAdapter($sm->get('zfcuser_zend_db_adapter'));
//                    $authAdapter->initStorage($sm->get('AuthSessionStorage'));
//
//                    return $authAdapter;
//                },
                'default_hydrator' => function ($sm) {
                    $hydrator = new ClassMethods();
                    return $hydrator;
                },
            ],
        ];
    }
}