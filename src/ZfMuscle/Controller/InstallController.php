<?php

namespace ZfMuscle\Controller;

use Zend\View\Model\ViewModel;
use ZfcUser\Controller\UserController as ZfcUserController;
use Zend\Mvc\MvcEvent;

/**
 * Description of InstallController
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class InstallController extends ZfcUserController
{
    const CONTROLLER_NAME    = 'zfmuscle';
    
    /**
     * Route to login page
     */
    const ROUTE_LOGIN = 'zfmuscle/users/login'; //\ZfcUser\Controller\UserController::ROUTE_LOGIN;
    
    protected $service;

    /**
     * A whitelist of unguarded actions
     *
     * @var array
     */
    protected $publicActions = array();
    
    /**
     * Flashmessanger helper
     * @var type 
     */
    protected $flashMessenger;


    public function onDispatch(MvcEvent $e)
    {
        /**
         * If no administrative user exists in the database,
         * then redirect to register page for a first time run
         */
        $filters = array();
        $users = $this->_getUsers($filters);
        if (!$users)
        {
            /**
             * Since it is assumed that this is our installation,
             * We need to insert our default roles in the database
             * since who ever installs this app is considered an administrator
             */
            $roleService = $this->getRoleService();
            $roles = $this->getDefaultRoles();

            // if the returned value is an array and it isn't empty
            if (is_array($roles) && $roles)
            {
                try
                {
                    $lastId = '';
                    foreach ($roles as $role)
                    {
                        $lastId = $roleService->save($role, true, $lastId)->getId();
                    }
                }
                catch (Exception $ex)
                {
                    var_dump($ex); die;
                }
            }

            $this->publicActions[] = 'index';
            $this->layout('layout/login_register');
        }
        else
        {
            $this->layout('layout/login_register');
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        
        /** INSTANTIATE FLASH MESSENGER **/
        $this->flashMessenger = $this->flashMessenger();
        
        return parent::onDispatch($e);
    }
    
    protected function _getUsers(array $filters) {
        $service = $this->getService();
        $users = $service->isUsersExist($filters);
        return $users;
    }
    
    public function indexAction()
    {
        // if registration is disabled
        if (!$this->getOptions()->getEnableRegistration())
        {
            return array('enableRegistration' => false);
        }
        
        $user = null;
        
        try
        {
            $request = $this->getRequest();
            $service = $this->getUserService();
            $form = $this->getRegisterForm();
            
//            var_dump($form); die;
            if ($request->isPost())
            {
                $post = $request->getPost();

                if (is_object($post))
                {
                    $post = get_object_vars($post);
                }
                
                $user = $service->register($post);

                if (!$user)
                {
                    return array(
                        'registerForm' => $form,
                        'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                    );
                }
                
                /**
                 * Since it is assumed that this is our installation,
                 * We need to set our first user role to administrator
                 */
                $roleService = $this->getRoleService();
                $adminRole = $roleService->getDefaultAdminRole();
                
                if ($adminRole)
                {
                    $data = array(
                        'id' => $user,
                        'role' => $adminRole,
                    );
                    $this->getService()->save($data);
                }
                
                $this->flashMessenger->setNamespace('success');
                $this->flashMessenger->addMessage('Congratulations! Your Application is ready. Please login below for access to more control.');
                return $this->redirect()->toRoute(static::ROUTE_LOGIN);

            }
        } catch (Exception $ex) {
            var_dump($ex->getMessages()); die;
        }
        
        return array(
            'registerForm' => $form,
            'enableRegistration' => $this->getOptions()->getEnableRegistration(),
        );
    }

    public function getService()
    {
        if (!$this->service instanceof ZfMuscleEntityInterface) {
            $this->service = $this->getServiceLocator()->get('zfmuscle_user_service');
        }
        return $this->service;
    }

    protected function getRoleService()
    {
        return $this->getServiceLocator()->get('zfmuscle_role_service');
    }
    
    /**
     * Returns ZfMuscle default roles for insert
     * @var type array
     */
    protected function getDefaultRoles()
    {
        $config = $this->getServiceLocator()->get('config');
        $roles = $config['zfmuscle']['default_roles'];
        return $roles;
    }
}