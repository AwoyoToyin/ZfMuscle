<?php

namespace ZfMuscle\Factory;

use BjyAuthorize\Exception\InvalidArgumentException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfMuscle\Provider\Rule\DoctrineRuleProvider;
use BjyAuthorize\Provider\Rule\Config;

class DoctrineRuleProviderAdapterFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        //just setting up our config, move along move along...
        $config = $serviceLocator->get('Config');
        $bjyConfig = $config['bjyauthorize'];

        //making sure we have proper entries in our config... 
        //move along "nothing to see" here....
        if (!isset($bjyConfig['rule_providers']['zfmuscle_rule_provider_adapter'])) {
            throw new InvalidArgumentException(
            'Config for "zfmuscle_rule_provider_adapter" not set'
            );
        }

        //yep all is well we load our own module config here
        $providerConfig = $bjyConfig['rule_providers']['zfmuscle_rule_provider_adapter'];

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
        $default = $bjyConfig['rule_providers']['BjyAuthorize\Provider\Rule\Config'];

        //orp -- object repository provider
        //here we get our class that preps the object repository for us
        $orp = new DoctrineRuleProvider($objectManager->getRepository($providerConfig['rule_entity_class']), $objectManager, $default);

        //here we pull the rules out of that object we've created above
        //rules are in the same format BjyAuthorize expects
        $ruleConfigs = $orp->getRules();
        
        //here pass our resources to BjyAuthorize's own Resource Config.
        //It will not know the difference if we got the rules from Config or from Doctrine or elsewhere,
        //as long as $resources are in the form it expects.
        return new Config($ruleConfigs);
    }

}