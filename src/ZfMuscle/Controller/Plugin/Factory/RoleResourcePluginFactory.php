<?php

namespace ZfMuscle\Controller\Plugin\Factory;

use ZfMuscle\Controller\Plugin\RoleResourcePlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class RoleResourcePluginFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $pluginManager)
    {
        $serviceLocator = $pluginManager->getServiceLocator();
        $config = $serviceLocator->get('ZfMuscle\Config');
        return new RoleResourcePlugin($config, $serviceLocator);
    }
}