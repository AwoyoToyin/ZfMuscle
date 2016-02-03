<?php

namespace ZfMuscle\Event\Guard;

use ZfMuscle\Exception\NeedsInstallException;
use BjyAuthorize\Guard\GuardInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Checks if application module is installed
 * during {@see \Zend\Mvc\MvcEvent::EVENT_DISPATCH}
 */
class Application implements GuardInterface
{
    /**
     * Marker for installation needed to be run
     */
    const ERROR = 'needs-install';

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -1001);
    }

    /**
     * {@inheritDoc}
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     *
     * @param MvcEvent $event
     *
     * @return void
     */
    public function onRoute(MvcEvent $event)
    {
        $service    = $this->serviceLocator->get('zfmuscle_application_service');

        if (!$service->isInstalled())
        {
            $service->run();
        }

        $event->setError(static::ERROR);
        $event->setParam('exception', new NeedsInstallException('You need install the application'));

        /* @var $app \Zend\Mvc\Application */
        $app = $event->getTarget();

        $app->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }
}
