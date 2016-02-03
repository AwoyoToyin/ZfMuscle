<?php

namespace ZfMuscle\Service;

use ZfMuscle\Service\AbstractCrudService;

/**
 * Description of ResourceService
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class ResourceService extends AbstractCrudService
{
    public function save(array $data)
    {
        // check if controller action exists, do nothing if it exists
        $filters['controller'] = array(
            'strategy'  => 'Equals',
            'value'     => $data['controller_id']
        );
        $filters['title'] = array(
            'strategy'  => 'Equals',
            'value'     => $data['title']
        );
        $selection = $this->provider->selectAll($filters);
        $action = $this->provider->query($selection)->getOneOrNullResult();
//        var_dump($action); die;
        if ($action)
        {
            return $action;
        }
        
        $entity = $this->provider->createEntity();
        $entity->exchangeArray($data);
        if (isset($data['controller_id']) && !empty($data['controller_id']))
        {
            $controller = $this->getServiceManager()->get('doctrine.entitymanager.orm_default')->getReference('ZfMuscle\Entity\ObjectRepositoryController', $data['controller_id']);
            $entity->setController($controller);
        }
        $this->provider->save($entity);
        return $entity;
    }
}
