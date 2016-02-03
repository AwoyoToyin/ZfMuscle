<?php

namespace ZfMuscle\Controller;

use Zend\View\Model\ViewModel;
use ZfMuscle\Controller\AbstractSecuredActionController;

/**
 * Description of SystemController
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class SystemController extends AbstractSecuredActionController
{
    const ROUTE_INDEX       = 'zfmuscle/system';
    
    public function indexAction()
    {
        return new ViewModel(array());
    }
    
    public function updateResourceAction()
    {
        $output = array();
        try
        {
            // retrieve all loaded modules, their controllers and actions
            $resources = $this->RoleResourcePlugin();

            if (count($resources) && !empty($resources))
            {
                foreach ($resources as $pointer => $module)
                {
                    $data = array();
                    $data['title'] = $pointer;
                    $this->service_definition = 'zfmuscle_module_service';
                    $service = $this->getService();
                    $moduleEntity = $service->save($data);

                    if (!$moduleEntity)
                    {
                        $output['error'] = true;
                    }
                    else
                    {
                        $output['error'] = false;
                        if (!empty($module))
                        {
                            foreach ($module as $controller)
                            {
                                foreach ($controller as $key => $value)
                                {
                                    if ($key === 'alias')
                                    {
                                        $data['alias'] = $value;
                                        $data['module_id'] = $moduleEntity->getId();
                                        $this->service_definition = 'zfmuscle_controller_service';
                                        $service = $this->getService();
                                        $controllerEntity = $service->save($data);
                                    }

                                    if (!$controllerEntity)
                                    {
                                        $output['error'] = true;
                                    }
                                    else
                                    {
                                        $output['error'] = false;
                                        if ($key === 'actions' && !empty($key))
                                        {
                                            foreach ($value as $action)
                                            {
                                                if (!ctype_lower($action)) // if action contains an uppercase letter
                                                {
                                                    // split action at the upper cased letter
                                                    $splits = preg_split("/(?<=[a-z])(?![a-z])/", $action, -1, PREG_SPLIT_NO_EMPTY);
                                                    $size = sizeof($splits);
                                                    if ($size > 1)
                                                    {
                                                        $lower = array();
                                                        foreach ($splits as $k => $split)
                                                        {
                                                            if ($k !== 0)
                                                            {
                                                                $lower[] = '-'.strtolower($splits[$k]);
                                                            }
                                                            else
                                                            {
                                                                $lower[] = strtolower($splits[$k]);
                                                            }
                                                        }
                                                        $data['title'] = implode('', $lower);
                                                    }
                                                    else
                                                    {
                                                        $data['title'] = strtolower($splits[0]);
                                                    }
                                                }
                                                else
                                                {
                                                    $data['title'] = strtolower($action);
                                                }
                                                
                                                $data['controller_id'] = $controllerEntity->getId();
                                                $this->service_definition = 'zfmuscle_resource_service';
                                                $service = $this->getService();
                                                $resourceEntity = $service->save($data);

                                                if (!$resourceEntity)
                                                {
                                                    $output['error'] = true;
                                                }
                                                else
                                                {
                                                    $output['error'] = false;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        catch (Exception $exc)
        {
            var_dump($exc->getMessages()); die;
        }
        
        if (isset($output['error']) && !empty($output['error']) && $output['error'] === true)
        {
            $this->namespace = 'danger';
            $this->setFlashmessage('There was an error updating object role resources.');
        }
        else
        {
            $this->namespace = 'success';
            $this->setFlashmessage('Object role resources updated successfully.');
        }
        
        return $this->redirect()->toRoute(static::ROUTE_INDEX);
    }
}