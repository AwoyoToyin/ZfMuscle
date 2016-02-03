<?php

namespace ZfMuscle\Core\View\Helper;

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
    
    public function isAllowed($controllers, $method=null)
    {
        $serviceManager = $this->getServiceManager();
        $service = $serviceManager->get('BjyAuthorize\Service\Authorize');
        
        /** Get current loggedIn role **/
        $identity = $service->getIdentityProvider()->getIdentityRoles();
        $roleId = $identity[0]->getRoleId();
        $parentId = $identity[0]->getParent()->getRoleId();
        
        $result = [];
        
        foreach ($controllers as $controller)
        {
            $method = !is_null($method) ? $method : 'index';
            
            if (($roleId == $this->getDefaultAdminRole() || $parentId == $this->getDefaultAdminRole()) ||
                $service->isAllowed($controller) || $service->isAllowed($controller, $method))
            {
                $result['allow'] = true;
            }
            else
            {
                $result['deny'] = false;
            }
        }
        return $result;
    }
    
    protected function getDefaultAdminRole()
    {
        $serviceManager = $this->getServiceManager();
        $config = $serviceManager->get('config');
        return $config['zfmuscle']['default_admin_role'];
    }
}