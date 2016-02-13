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
    public function index($page=1, array $filters=[])
    {
        return parent::index($page, $filters);
    }

    public function fetchAllResources(array $filters=[], $orderBy=[], $groupBy=[])
    {
        return parent::fetchByGroup($filters, $orderBy, $groupBy);
    }

    public function save(array $data)
    {
        // check if resource already exist for the set role, do nothing if it does
        $filters['role'] = [
            'strategy'  => 'Equals',
            'value'     => $data['role_id']
        ];
        $filters['resource_id'] = [
            'strategy'  => 'Equals',
            'value'     => $data['resource_id']
        ];
        $filters['controller_id'] = [
            'strategy'  => 'Equals',
            'value'     => $data['controller_id']
        ];
        $selection = $this->provider->selectAll($filters);
        $result = $this->provider->query($selection)->getOneOrNullResult();
        if ($result)
        {
            return $result;
        }
        
        $entity = $this->provider->createEntity();
        $entity->exchangeArray($data);
        if (isset($data['role_id']) && !empty($data['role_id']))
        {
            $role = $this->getServiceManager()->get('doctrine.entitymanager.orm_default')->getReference('ZfMuscle\Entity\Role', $data['role_id']);
            $entity->setRole($role);
        }
        $this->provider->save($entity);
        return $entity;
    }
}
