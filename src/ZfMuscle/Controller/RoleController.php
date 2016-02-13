<?php

namespace ZfMuscle\Controller;

use ZfMuscle\Controller\AbstractSecuredActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;

/**
 * Description of RoleController
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class RoleController extends AbstractSecuredActionController
{
    const CONTROLLER_NAME   = 'zfmuscle-role';
    
    const ROUTE_INDEX       = 'zfmuscle/permission/roles';
    
    public function onDispatch(MvcEvent $e)
    {
        $this->service_definition = 'zfmuscle_role_service';
        $this->template_path = 'zf-muscle/role/list';
        parent::onDispatch($e);
    }
    
    public function indexAction()
    {
        return parent::indexAction();
    }
    
    public function entryAction()
    {
        $service = $this->getService();
        $form = $service->getCrudForm('zfmuscle_role_form');

        $id = $this->params()->fromRoute('id', null);
        
        try
        {
            $request = $this->getRequest();
            if ($request->isPost())
            {
                $post = $request->getPost();
                
                if (is_object($post))
                {
                    $post = get_object_vars($post);
                }
                
//                $entity = $service->getEntityClass();
//                $form->setHydrator($service->getFormHydrator());
//                $form->bind($entity);
                $form->setData($post);
                
                if ($request->isXmlHttpRequest())
                {   
                    if (!$form->isValid()) {
//                        var_dump($form->getMessages()); die;
                        return new JsonModel(array(
                            'status' => 'ERROR',
                            'message' => 'Error submitting form. Please check the form and try again.'
                        ));
                    }
                    
                    $post = $form->getData();
                    
                    $actions = $request->getPost('actions');
                    if (!empty($actions))
                    {
                        $post['actions'] = $actions;
                    }
                    
                    $role = $service->save($post, false);

                    if (!$role)
                    {
                        return new JsonModel(array(
                            'status' => 'ERROR',
                            'message' => 'Internal Error encountered. Please check the form and try again.'
                        ));
                    }
                    
                    return new JsonModel(array(
                        'status' => 'SUCCESS',
                        'redirect' => '1',
                        'message' => 'Role saved successfully.'
                    ));
                }
                else
                {
                    if (!$form->isValid()) {
                        return false;
                    }
                    
                    $post = $form->getData();
                    $role = $service->save($post, false);
                    if (!$role)
                    {
                        return new ViewModel(array(
                            'form' => $form
                        ));
                    }
                    
                    $this->namespace = 'success';
                    $this->setFlashmessage('Role saved successfully.');
                    return $this->redirect()->toRoute(static::ROUTE_INDEX);
                }
            }
        }
        catch (Exception $ex)
        {
            var_dump($ex->getMessages()); die;
        }
        
        $resources = array();
        
        if ($id)
        {
            $entity = $service->read($id);
            if (!$entity)
            {
                $this->namespace = 'danger';
                $this->setFlashmessage('Internal Server Error.');
                return $this->redirect()->toRoute(static::ROUTE_INDEX);
            }
            $form->setHydrator($service->getFormHydrator());
            $form->bind($entity);
            
            $resources = $entity->getResources()->toArray();
        }
        
        return new ViewModel(array(
            'form' => $form,
            'resources' => $resources
        ));
    }

    public function deleteAction()
    {
        $service = $this->getService();
        $id = $this->params()->fromRoute('id', null);
        if ($id)
        {
            $entity = $service->read($id);
            if (!$entity)
            {
                $this->namespace = 'danger';
                $this->setFlashmessage('Internal Server Error.');
            }
            
            $result = $service->delete($id);
            
            if ($result !== null && $result === false)
            {
                $this->namespace = 'danger';
                $this->setFlashmessage('Internal Server Error.');
            }
            else
            {
                $this->namespace = 'success';
                $this->setFlashmessage('Role deleted successfully.');
            }
        }
        else
        {
            $this->namespace = 'danger';
            $this->setFlashmessage('Internal Server Error.');
        }
        return $this->redirect()->toRoute(static::ROUTE_INDEX);
    }
}