<?php

namespace ZfMuscle\Service;

use ZfMuscle\Event\Guard\Application;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Guard\Controller}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ApplicationGuardServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Guard\Controller
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Application($serviceLocator);
    }
}
