<?php

namespace ZfMuscle;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\FlashMessenger;

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
            $app->getEventManager()->attach($sm->get($listener));
        }



        $zfcServiceEvents = $sm->get('zfcuser_user_service')->getEventManager();
        // To validate new field
        $sharedManager->attach('ZfcUser\Form\RegisterFilter','init', function($e) {
            $filter = $e->getTarget();
            $filter->add(array(
                'name'       => 'firstname',
                'required'   => true,
                'allowEmpty' => false,
                'filters'    => array(array('name' => 'StringTrim')),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                    )
                ),
            ));
            $filter->add(array(
                'name'       => 'lastname',
                'required'   => true,
                'allowEmpty' => false,
                'filters'    => array(array('name' => 'StringTrim')),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                    )
                ),
            ));
        });

        // Store the field
        $entityManager = $sm->get('doctrine.entitymanager.orm_default');

        $zfcServiceEvents->attach('register', function($e) use ($entityManager) {
            $form = $e->getParam('form');
            $user = $e->getParam('user');

            /* @var $user \ZfMuscle\Entity\User */
            $user->setUsername($form->get('username')->getValue());
            $user->setFirstname($form->get('firstname')->getValue());

            // if user role field exists
            if ($form->get('role')->getValue())
            {
                $role = $entityManager->getReference('ZfMuscle\Entity\Role', $form->get('role')->getValue());
                $user->addRole($role);
            }
        });


        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Show flashmessages in the view
        $eventManager->attach(MvcEvent::EVENT_RENDER, function($e) {
            $flashMessenger = new FlashMessenger;

            $messages = array();

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
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'RoleResourcePlugin' => 'ZfMuscle\Controller\Plugin\Factory\RoleResourcePluginFactory',
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'zfmuscle' => 'ZfMuscle\Factory\Controller\InstallControllerFactory',
                'zfmuscle-user' => 'ZfMuscle\Factory\Controller\UserControllerFactory',
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'RoleResourceHelper' => function ($sm) {
                    $helper = new \ZfMuscle\View\Helper\RoleResourceHelper();
                    $helper->setServiceLocator($sm);
                    return $helper;
                },
                'RoleHelper' => function ($sm) {
                    $helper = new \ZfMuscle\View\Helper\RoleHelper();
                    $helper->setServiceLocator($sm);
                    return $helper;
                }
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                
            ),
            'factories' => array(
                'Zend\Authentication\AuthenticationService' => function($sm) {
                    return $sm->get('doctrine.authenticationservice.orm_default');
                },
                'zfcuser_module_options' => function ($sm) {
                    $config = $sm->get('Configuration');
                    return new \ZfMuscle\Options\ModuleOptions(isset($config['zfcuser']) ? $config['zfcuser'] : array());
                },
                'zfmuscle_user_service' => function ($sm) {
                    $service = new \ZfMuscle\Service\UserService();
                    $service->setServiceManager($sm);
                    $provider = new \ZfMuscle\Provider\UserProvider();
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $provider->setEntityManager($em);
                    $service->setProvider($provider);
                    return $service;
                },
                'zfmuscle_role_service' => function ($sm) {
                    $service = new \ZfMuscle\Service\RoleService();
                    $service->setServiceManager($sm);
                    $formHydrator = $sm->get('default_hydrator');
                    $service->setFormHydrator($formHydrator);
                    $provider = new \ZfMuscle\Provider\RoleProvider();
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $provider->setEntityManager($em);
                    $service->setProvider($provider);
                    return $service;
                },
                'zfmuscle_resource_service' => function ($sm) {
                    $service = new \ZfMuscle\Service\ResourceService();
                    $service->setServiceManager($sm);
                    $provider = new \ZfMuscle\Provider\ResourceProvider();
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $provider->setEntityManager($em);
                    $service->setProvider($provider);
                    return $service;
                },
                'zfmuscle_application_service' => function ($sm) {
                    $service = new \ZfMuscle\Service\ApplicationService();
                    $service->setServiceManager($sm);
                    return $service;
                },
                'zfcuser_login_form' => function ($sm) {
                    $options = $sm->get('zfcuser_module_options');
                    $form = new \ZfMuscle\Form\Login(null, $options);
                    $form->setInputFilter(new \ZfcUser\Form\LoginFilter($options));
                    return $form;
                },
                'zfcuser_register_form' => function($sm){
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $options = $sm->get('zfcuser_module_options');
                    $form = new \ZfMuscle\Form\User($em, null, $options);
//                    $form->setCaptchaElement($sm->get('zfcuser_captcha_element'));
                    $form->setInputFilter(new \ZfcUser\Form\RegisterFilter(
                        new \ZfcUser\Validator\NoRecordExists(array(
                            'mapper' => $sm->get('zfcuser_user_mapper'),
                            'key'    => 'email'
                        )),
                        new \ZfcUser\Validator\NoRecordExists(array(
                            'mapper' => $sm->get('zfcuser_user_mapper'),
                            'key'    => 'username'
                        )),
                        $options
                    ));
                    return $form;
                },
                'zfmuscle_role_form' => function ($sm) {
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $form = new \ZfMuscle\Form\Role($em);
//                    $form->setServiceManager($sm);
                    return $form;
                },
                'default_hydrator' => function ($sm) {
                    $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
                    return $hydrator;
                },
            ),
        );
    }
}