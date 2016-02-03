<?php

namespace ZfMuscle\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;


class ApplicationServiceFactory implements FactoryInterface
{
    protected $_appConfig;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('ZfMuscle\Config'); // get zfmuscle.global.config data

        if (array_key_exists('listeners', $config))
        {
            $this->_appConfig = $serviceLocator->get('ApplicationConfig'); // get application.config
            if (array_key_exists('listeners', $this->_appConfig))
            {
                foreach ($config['listeners'] as $listener)
                {
                    $this->_appConfig['listeners'][] = $listener;
                }
            }
            else {
                $this->_appConfig['listeners'] = $config['listeners'];
            }
            $serviceManager = new ServiceManager(new ServiceManagerConfig());
            $serviceManager->setService('ApplicationConfig', $this->_appConfig);
        }
        return $this->_appConfig;
    }
}
