<?php

namespace ZfMuscle\Factory;

use BjyAuthorize\Exception\InvalidArgumentException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfMuscle\Provider\Resource\DoctrineResourceProvider;
use BjyAuthorize\Provider\Resource\Config;

class DoctrineResourceProviderAdapterFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        //just setting up our config, move along move along...
        $config = $serviceLocator->get('Config');
        $bjyConfig = $config['bjyauthorize'];

        //making sure we have proper entries in our config... 
        //move along "nothing to see" here....
        if (!isset($bjyConfig['resource_providers']['zfmuscle_resource_provider_adapter'])) {
            throw new InvalidArgumentException(
            'Config for "zfmuscle_resource_provider_adapter" not set'
            );
        }

        //yep all is well we load our own module config here
        $providerConfig = $bjyConfig['resource_providers']['zfmuscle_resource_provider_adapter'];

        if (!isset($providerConfig['object_manager'])) {
            throw new InvalidArgumentException('object_manager not set in the zfmuscle guards config.');
        }

        /* @var $objectManager \Doctrine\Common\Persistence\ObjectManager */
        $objectManager = $serviceLocator->get($providerConfig['object_manager']);

        //orp -- object repository provider
        //here we get our class that preps the object repository for us
        $orp = new DoctrineResourceProvider($serviceLocator->get('zfmuscle_resource_service'), $objectManager);

        //here we pull the rules out of that object we've created above
        //rules are in the same format BjyAuthorize expects
        $resources = $orp->getResources();
        
        //here pass our resources to BjyAuthorize's own Resource Config.  
        //It will not know the difference if we got the rules from Config or from Doctrine or elsewhere,  
        //as long as $resources are in the form it expects.
        return new Config($resources);
    }

}