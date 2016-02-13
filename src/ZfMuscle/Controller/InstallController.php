<?php

namespace ZfMuscle\Controller;

use Zend\View\Model\ViewModel;
use ZfcUser\Controller\UserController as ZfcUserController;
use ZfMuscle\Entity\ZfMuscleEntityInterface;
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
        $this->layout('layout/login_register');
        
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
            $service = $this->getService('zfmuscle_user_service');
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
                $roleService = $this->getService('zfmuscle_role_service');
                $adminRoleId = $roleService->getDefaultAdminRole();
                
                if ($adminRoleId)
                {
                    $data = array(
                        'id' => $user,
                        'role' => $adminRoleId,
                    );
                    $service->save($data);
                }

                /**
                 * Generate Installation File
                 */
                $appService = $this->getService('zfmuscle_application_service');
                $appService->generateXml(); // generate the file
                
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

    public function getService($definition)
    {
        if (!$this->service instanceof ZfMuscleEntityInterface) {
            $this->service = $this->getServiceLocator()->get($definition);
        }
        return $this->service;
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