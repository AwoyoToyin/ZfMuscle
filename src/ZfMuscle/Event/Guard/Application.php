<?php

namespace ZfMuscle\Event\Guard;

use ZfMuscle\Exception\NeedsInstallException;
use BjyAuthorize\Guard\GuardInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Http\Response;
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
     * Installation route
     */
    const MODULE_MATCH = 'zfmuscle';

    /**
     * Installation route
     */
    const INSTALL_ROUTE = 'zfmuscle/install';

    /**
     * Login route
     */
    const LOGIN_ROUTE = 'zfmuscle/users/login';

    /**
     * Dashboard route
     */
    const DASHBOARD_ROUTE = 'zfmuscle/dashboard';

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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -900);
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
        $match      = $event->getRouteMatch();
        $routeName  = $match->getMatchedRouteName();

        if (!$service->isInstalled() && $routeName === static::INSTALL_ROUTE)
        {
            $service->run();
            return;
        }
        elseif (!$service->isInstalled())
        {
            $url = $event->getRouter()->assemble(array(), array('name' => static::INSTALL_ROUTE));
            $this->_redirectTo($event, $url);
        }
        else
        {
            $routeArray = explode('/', $routeName);
            if (in_array(static::MODULE_MATCH, $routeArray))
            {
                $auth = $this->serviceLocator->get('zfcuser_auth_service');
                if ($routeName === static::LOGIN_ROUTE && $auth->hasIdentity())
                {
                    $url = $event->getRouter()->assemble(array(), array('name' => static::DASHBOARD_ROUTE));
                }
                elseif ($routeName === static::LOGIN_ROUTE)
                {
                    return;
                }
                elseif (!$auth->hasIdentity())
                {
                    $url = $event->getRouter()->assemble(array(), array('name' => static::LOGIN_ROUTE));
                }
                $this->_redirectTo($event, $url);
            }
            else
            {
                return;
            }
        }
    }

    protected function _redirectTo(MvcEvent $event, $url)
    {
        $response = $event->getResponse();

        $response = $response ?: new Response();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);
        $response->sendHeaders();
        $event->setResponse($response);

        // To avoid additional processing
        // we can attach a listener for Event Route with a high priority
        $stopCallBack = function($event) use ($response)
        {
            $event->stopPropagation();
            return $response;
        };

        //Attach the "break" as a listener with a high priority
        $event->getApplication()->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, $stopCallBack);
    }
}
