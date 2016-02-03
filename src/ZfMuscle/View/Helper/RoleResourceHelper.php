<?php

namespace ZfMuscle\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of RoleResourceHelper
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class RoleResourceHelper extends AbstractHelper implements ServiceLocatorAwareInterface
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
    
    public function __invoke($roleResources)
    {
        // helpermanager that gives access to other view helpers
        $helperPluginManager = $this->getServiceLocator();
        // servicemanager gives access to a wide range of things.
        $serviceManager = $helperPluginManager->getServiceLocator();
        $moduleService = $serviceManager->get('zfmuscle_module_service');
        $resources = $moduleService->fetchAll();
        
        return $this->getView()->render('zf-muscle/role/resource/widget', array('resources' => $resources, 'roleResources' => $roleResources));
    }
}