<?php

namespace ZfMuscle\Event;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfMuscle\Service\ZfMuscleServiceInterface;
use Zend\Cache\Storage\StorageInterface;

abstract class AbstractSharedListener implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    /**
     * Instance of the service object
     */
    protected $service;
    
    /**
     * Definition of the service class
     */
    protected $service_definition;
    
    /**
     * Definition of the service locator
     */
    protected $serviceLocator;
    
    protected $cache;
    
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('cacheUserPermission', array($this, 'onCacheUserPermission'), -2000);
    }
    
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener)
        {
            if ($events->detach($listener))
            {
                unset($this->listeners[$index]);
            }
        }
    }
    
    public function onCacheUserPermission(EventInterface $e)
    {
        if (!$this->cache instanceof StorageInterface)
        {
            $this->cache = $this->getServiceLocator()->get('Zend\Cache\Storage\Filesystem');
        }
        
        return $this->getCache($e);
    }

    public function getCache($e)
    {
        $identifier = $e->getParam('user');
        $identifierId = $identifier->getId();
        $cached = $this->cache->getItem($identifierId);
        
        if (!$cached)
        {
            $roles = $identifier->getRoles();
            $role = '';
            if (!empty($roles))
            {
                foreach ($roles as $entity)
                {
                    if ($entity->getResources())
                    {
                        $role = $entity;
                        break;
                    }
                }
            }
            
            if ($role)
            {
                $this->cache->setItem($identifierId, $role);
            }
        }
        
        return $this->cache;
    }

    public function getServiceLocator() {
        if (!$this->serviceLocator)
        {
            $this->setServiceLocator(new ServiceLocatorInterface());
        }
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        if (!$this->serviceLocator instanceof ServiceLocatorInterface)
        {
            $this->serviceLocator = $serviceLocator;
        }
        return $this->serviceLocator;
    }

    public function getService()
    {
        return $this->service = $this->getServiceLocator()->get($this->service_definition);
    }

}