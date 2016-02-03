<?php

namespace ZfMuscle\Factory;

use BjyAuthorize\Exception\InvalidArgumentException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfMuscle\Provider\Route\DoctrineGaurdProvider;
use BjyAuthorize\Guard\Route;

class DoctrineRouteGuardAdapterFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        //just setting up our config, move along move along...
        $config = $serviceLocator->get('Config');
        $bjyConfig = $config['bjyauthorize'];

        //making sure we have proper entries in our config... 
        //move along "nothing to see" here....
        if (!isset($bjyConfig['guards']['zfmuscle_route_guard_adapter'])) {
            throw new InvalidArgumentException(
            'Config for "zfmuscle_route_guard_adapter" not set'
            );
        }

        //yep all is well we load our own module config here
        $providerConfig = $bjyConfig['guards']['zfmuscle_route_guard_adapter'];

        //more specific checks on config
        if (!isset($providerConfig['rule_entity_class'])) {
            throw new InvalidArgumentException('rule_entity_class not set in the zfmuscle guards config.');
        }

        if (!isset($providerConfig['object_manager'])) {
            throw new InvalidArgumentException('object_manager not set in the zfmuscle guards config.');
        }

        /* @var $objectManager \Doctrine\Common\Persistence\ObjectManager */
        $objectManager = $serviceLocator->get($providerConfig['object_manager']);
        
        // get any predefined/default rules
        $default = $bjyConfig['guards']['BjyAuthorize\Guard\Route'];
        
        //orp -- object repository provider
        //here we get our class that preps the object repository for us
        $orp = new DoctrineGaurdProvider($objectManager->getRepository($providerConfig['rule_entity_class']), $objectManager, $default);

        //here we pull the rules out of that object we've created above
        //rules are in the same format BjyAuthorize expects
        $rules = $orp->getRules();

        //here pass our rules to BjyAuthorize's own Guard Controller.
        //It will not know the difference if we got the rules from Config or from Doctrine or elsewhere,  
        //as long as $rules are in the form it expects.
        return new Route($rules, $serviceLocator);
    }

}