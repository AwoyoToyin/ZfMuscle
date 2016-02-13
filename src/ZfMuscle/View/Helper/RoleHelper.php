<?php

namespace ZfMuscle\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of RoleHelper
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class RoleHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    
    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return CustomHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)  
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
    
    /**
     * Get the service locator.
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    protected function getServiceManager()
    {
        // helpermanager that gives access to other view helpers
        $helperPluginManager = $this->getServiceLocator();
        // servicemanager gives access to a wide range of things.
        return $helperPluginManager->getServiceLocator();
    }
    
    public function isAllowed($routes)
    {
        $serviceManager = $this->getServiceManager();
        $service = $serviceManager->get('BjyAuthorize\Service\Authorize');
        
        $result = [];
        
        foreach ($routes as $route)
        {
            if ($service->isAllowed('route/' . $route))
            {
                $result['allow'] = true;
            } else {
                $result['deny'] = false;
            }
        }
        return $result;
    }
}