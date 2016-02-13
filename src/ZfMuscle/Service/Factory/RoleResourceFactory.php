<?php

namespace ZfMuscle\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfMuscle\Service\RoleResource;

/**
 * Factory responsible of building the {@see \ZfMuscle\Service\RoleResource} service
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class RoleResourceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \ZfMuscle\Service\RoleResource
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('ZfMuscle\Config');
        return new RoleResource($config, $serviceLocator);
    }
}
