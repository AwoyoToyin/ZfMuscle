<?php

namespace ZfMuscle\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
//use Zend\Json\Json;
use ZfcUser\Controller\UserController as ZfcUserController;
use Zend\Mvc\MvcEvent;

/**
 * Description of UserController
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class UserController extends ZfcUserController
{
    const ROUTE_LOGIN           = 'zfmuscle/users/login';
    const ROUTE_INSTALL         = 'zfmuscle/install';

    const CONTROLLER_NAME       = 'zfmuscle-user';

    const LOGIN_REDIRECT_ROUTE  = 'zfmuscle/dashboard';
    
    const ROUTE_INDEX           = 'zfmuscle/permission/users';

    
    /**
     * Instance of the service object
     *
     * @var \ZfMuscle\Service\UserService
     */
    protected $service;
    
    /**
     * Flashmessanger helper
     * @var type 
     */
    protected $flashMessenger;
    
    /**
     * A whitelist of unguarded actions
     *
     * @var array
     */
    protected $publicActions = array('login');

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
            $this->publicActions[] = 'register';

            return $this->redirect()->toRoute(static::ROUTE_INSTALL);
        }

        $action = $this->params('action');

        if (!$this->_hasIdentity() && ($action != 'login') && !in_array($action, $this->publicActions))
        {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $this->layout('layout/dashboard');

        /** INSTANTIATE FLASH MESSENGER **/
        $this->flashMessenger = $this->flashMessenger();

        return parent::onDispatch($e);
    }
    
    protected function _getUsers(array $filters)
    {
        $service = $this->getService();
        $users = $service->isUsersExist($filters);
        return $users;
    }

    public function indexAction()
    {
        $this->_isLoggedIn();
        
        $view = new ViewModel();

        $request = $this->getRequest();
        
        $service = $this->getService();
        $page = $this->params()->fromRoute('page', 1);
        $filters = array();
        
        $paginator = $service->index($page, $filters);
        
        if ($request->isXmlHttpRequest())
        {
            $view->setVariables(array('paginator' => $paginator, 'page' => $page));
            $view->setTemplate('zf-muscle/user/list');
            $view->setTerminal(true);
        } else
        {
            $tableView = new ViewModel();
            $tableView->setVariables(array('paginator' => $paginator, 'page' => $page));
            $tableView->setTemplate('zf-muscle/user/list');
            $view->addChild($tableView, 'list');
        }
//        $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('ZfMuscle | Permission - Users');
        return $view;
    }

    public function loginAction()
    {
        if ($this->_hasIdentity())
        {
            return $this->redirect()->toRoute(static::LOGIN_REDIRECT_ROUTE);
        }
        
        $request = $this->getRequest();
        $form = $this->getLoginForm();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect'))
        {
            $redirect = $request->getQuery()->get('redirect');
        } else
        {
            $redirect = false;
        }

        if (!$request->isPost())
        {
            $viewModel = new ViewModel(array(
                'loginForm' => $form,
                'redirect' => $redirect,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
            ));
            
            $this->layout('layout/login_register');
            return $viewModel;
        }
        
        $form->setData($request->getPost());
        
        // if ajax request was used
        if ($request->isXmlHttpRequest())
        {
            if (!$form->isValid())
            {
                // return a simple error not exposing what actually whent wrong. We don't want a criminal mind sniffing around
                return new JsonModel(array(
                    'status' => 'ERROR',
                    'message' => $this->failedLoginMessage,
                ));
            }
            
            // clear adapters
            $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
            $this->zfcUserAuthentication()->getAuthService()->clearIdentity();
            
            $this->publicActions[] = 'authenticate';
            $authenticated = $this->authenticateAction();
            
            return new JsonModel($authenticated);
        }
        else
        {
            if (!$form->isValid())
            {
                $this->flashMessenger->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
                return $this->redirect()->toUrl($this->url()->fromRoute(static::ROUTE_LOGIN) . ($redirect ? '?redirect=' . rawurlencode($redirect) : ''));
            }
            
            // clear adapters
            $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
            $this->zfcUserAuthentication()->getAuthService()->clearIdentity();
            return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate'));
        }
    }

    /**
     * Register new user
     */
    public function registerAction()
    {
        // if the user is logged in, we don't need to register
        if (!$this->_hasIdentity())
        {
            // redirect to the login page
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        
        // if registration is disabled
        if (!$this->getOptions()->getEnableRegistration())
        {
            return array('enableRegistration' => false);
        }
        
        $form = $this->getRegisterForm();
        $id = $this->params()->fromRoute('id', null);
        
        try
        {
            $service = $this->getUserService();
            $request = $this->getRequest();
            $form->get('submit')->setLabel('Save User');
            
            if ($request->isPost())
            {
                $post = $request->getPost();

                if (is_object($post))
                {
                    $post = get_object_vars($post);
                }
                
                if (isset($post['id']) && !empty($post['id']))
                {
                    $service = $this->getService();
                    $user = $service->save($post);
                }
                else
                {
                    $user = $service->register($post);
                }
                
                if ($request->isXmlHttpRequest())
                {
                    if (!$user)
                    {
                        return new JsonModel(array(
                            'status' => 'ERROR',
                            'message' => 'Internal Error encountered. Please check the form and try again.'
                        ));
                    }
                    
                    return new JsonModel(array(
                        'status' => 'SUCCESS',
                        'redirect' => '1',
                        'message' => 'User saved successfully.'
                    ));
                }
                else
                {
                    if (!$user)
                    {
                        return array(
                            'registerForm' => $form,
                            'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                        );
                    }
                    
                    $this->flashMessenger->setNamespace('success');
                    $this->flashMessenger->addMessage('User saved successfully.');
                    return $this->redirect()->toRoute(static::ROUTE_INDEX);
                }
            }
        } catch (Exception $ex) {
            var_dump($ex->getMessages()); die;
//            $this->flashMessenger()->setNamespace('zfcuser-register-form')->addMessage($this->failedLoginMessage);
        }
        
        $vm = new ViewModel();
        
        if ($id)
        {
            $service = $this->getService();
            $entity = $service->read($id);
            if (!$entity)
            {
                $this->namespace = 'danger';
                $this->setFlashmessage('Internal Server Error.');
                return $this->redirect()->toRoute(static::ROUTE_INDEX);
            }
            $vm->setTemplate('zf-muscle/user/edit');
            
            $form->setHydrator($service->getFormHydrator());
            $form->bind($entity);
        }
        
        return $vm->setVariables(array(
            'registerForm' => $form,
            'enableRegistration' => $this->getOptions()->getEnableRegistration(),
        ));
    }

    public function authenticateAction()
    {
        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        $result = $adapter->prepareForAuthentication($this->getRequest());

        // Return early if an adapter returned a response
        if ($result instanceof Response) {
            return $result;
        }

        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
        
        if ($this->getRequest()->isXmlHttpRequest())
        {
            if (!$auth->isValid())
            {
                $adapter->resetAdapters();
                $failed = array(
                    'status' => 'ERROR',
                    'message' => $this->failedLoginMessage,
                );
                return $failed;
            }
            
            $identity = $this->zfcUserAuthentication()->getIdentity();
            
            // cache user permissions
            $this->getEventManager()->trigger('cacheUserPermission', $this, array('user' => $identity));
            
            $name = $identity->getFirstname()." ".$identity->getLastname();
            $success = array(
                'status' => 'SUCCESS',
                'redirect' => '1',
                'name' => $name,
                'message' => 'Howdy! Log In successful. Redirecting...'
            );
            
            return $success;
        }
        else
        {
            if (!$auth->isValid())
            {
                $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
                $adapter->resetAdapters();
                return $this->redirect()->toUrl($this->url()->fromRoute(static::ROUTE_LOGIN) . ($redirect ? '?redirect=' . rawurlencode($redirect) : ''));
            }
            
            // cache user permissions
            $this->getEventManager()->trigger('cacheUserPermission', $this, array('user' => $identity));
            
            if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect)
            {
                return $this->redirect()->toRoute($redirect);
            }

            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }
    }
    
    /**
     * Secures actions from unathorised access
     * @return null|string
     */
    protected function _hasIdentity()
    {
        return $this->zfcUserAuthentication()->hasIdentity();
    }
    
    protected function _isLoggedIn()
    {
        $action = $this->params('action');
        if (!in_array($action, $this->publicActions) && !$this->_hasIdentity())
        {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        return false;
    }

    public function getService()
    {
        if (!$this->service instanceof ZfMuscleEntityInterface)
        {
            $this->service = $this->getServiceLocator()->get('zfmuscle_user_service');
        }
        return $this->service;
    }

}
