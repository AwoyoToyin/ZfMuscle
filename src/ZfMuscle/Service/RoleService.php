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
    public function index($page=1, array $filters=array())
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
        
        if (isset($data['routes']) && !empty($data['routes']))
        {
            $entity->emptyResources();
            foreach ($data['routes'] as $route) {
                $action = $this->getServiceManager()->get('doctrine.entitymanager.orm_default')->getReference('ZfMuscle\Entity\Resource', $route);
                $entity->addResource($route);
            }
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
            foreach ($entity->getResources() as $resource) {
                $entity->removeResource($resource);
            }
        }
        return parent::delete($id);
    }
}
