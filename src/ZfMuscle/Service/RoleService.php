<?php

namespace ZfMuscle\Service;

use ZfMuscle\Service\AbstractCrudService;

/**
 * Description of RoleService
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class RoleService extends AbstractCrudService
{
    public function index($page=1, array $filters=[])
    {
        return parent::index($page, $filters);
    }

    public function save(array $data, $install=false, $lastId='')
    {
        $entity = parent::save($data);
        
        if ($install) // if this is a first time run
        {
            /**
             * check if a role with same name exist
             * If it does, do not insert, simply return
             */
            $filters['name'] = array(
                'strategy' => 'Equals',
                'value' => $data['name'],
            );
            $selection = $this->provider->selectAll($filters);
            $existing = $this->provider->query($selection)->getResult();

            if (count($existing))
            {
                foreach ($existing as $exist)
                {
                    if ($exist->getId())
                    {
                        return $exist;
                    }
                }
            }
        }
        
        $entity->exchangeArray($data);

        if ($lastId != '')
        {
            $data['parent'] = "$lastId";
        }

        if (isset($data['parent']) && !empty($data['parent']))
        {
            $role = $this->getServiceManager()->get('doctrine.entitymanager.orm_default')->getReference('ZfMuscle\Entity\Role', $data['parent']);
            $entity->setParent($role);
        }
        
        if (isset($data['resources']) && !empty($data['resources']))
        {
            $entity->emptyResources();
            $entity->addResource($data['resources']);
        }
        
        $this->provider->save($entity);
        return $entity;
    }
    
    public function delete($id)
    {
        $entity = $this->provider->findById($id);
        if (!$entity)
        {
            return;
        }
        
        if ($entity->getResources()->count())
        {
            foreach ($entity->getResources() as $resource)
            {
                $entity->removeResource($resource);
            }
        }
        return parent::delete($id);
    }
}
