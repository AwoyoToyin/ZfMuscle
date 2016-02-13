<?php

namespace ZfMuscle\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfMuscle\Controller\CrudControllerInterface;
use ZfMuscle\Entity\ZfMuscleEntityInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Description of AbstractSecuredActionController
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
abstract class AbstractSecuredActionController extends AbstractActionController implements CrudControllerInterface
{
    /**
     * Route to login page
     */
    const ROUTE_LOGIN = 'zfmuscle/users/login'; //\ZfcUser\Controller\UserController::ROUTE_LOGIN;
    
    /**
     * Instance of the service object
     */
    protected $service;
    
    /**
     * Definition of the service class
     */
    protected $service_definition;
    
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
    
    /**
     * Used in Flashmessanger helper
     * @var type
     */
    protected $namespace;
    
    /**
     * Definition of the template path to use
     * since we our module to use pagination
     */
    protected $template_path;
    
    /**
     * Definition of filter params if present
     * @var type Array
     */
    protected $filters = array();

    public function onDispatch(MvcEvent $e)
    {
//        $action = $this->params('action');
//        if (!in_array($action, $this->publicActions) && !$this->hasIdentity())
//        {
//            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
//        }
        
        $this->layout('layout/dashboard');
        
        /** INSTANTIATE FLASH MESSENGER **/
        $this->flashMessenger = $this->flashMessenger();
        
        return parent::onDispatch($e);
    }
    
    public function indexAction()
    {
        $view = new ViewModel();
        $request = $this->getRequest();
        
        $service = $this->getService();
        $page = $this->params()->fromRoute('page', 1);
        
        $paginator = $service->index($page, $this->filters);
        
        if ($request->isXmlHttpRequest())
        {
            $view->setVariables(array('paginator' => $paginator, 'page' => $page));
            $view->setTemplate($this->template_path);
            $view->setTerminal(true);
        }
        else
        {
            $tableView = new ViewModel();
            $tableView->setVariables(array('paginator' => $paginator, 'page' => $page));
            $tableView->setTemplate($this->template_path);
            $view->addChild($tableView, 'list');
        }
        return $view;
    }

    /**
     * Secures actions from unathorised access
     * 
     * @return null|string
     */
    public function hasIdentity()
    {
        return $this->zfcUserAuthentication()->hasIdentity();
    }
    
    public function setFlashmessage($message)
    {
        $this->flashMessenger->setNamespace($this->namespace)->addMessage($message);
    }

    public function getService()
    {
        if (!$this->service instanceof ZfMuscleEntityInterface)
        {
            $this->service = $this->getServiceLocator()->get($this->service_definition);
        }
        return $this->service;
    }
}