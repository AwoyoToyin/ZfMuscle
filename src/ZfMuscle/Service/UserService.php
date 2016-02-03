<?php

namespace ZfMuscle\Service;

use ZfcUser\Service\User as ZfcUserService;
use ZfMuscle\Service\ZfMuscleServiceInterface;
use ZfMuscle\Provider\ZfMuscleAbstractProvider;
//use Zend\Crypt\Password\Bcrypt;
//use Doctrine\Common\Collections\Criteria;

/**
 * Description of UserService
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class UserService extends ZfcUserService implements ZfMuscleServiceInterface
{
    /**
     * @var ZfMuscleAbstractProvider
     */
    protected $provider;
    
    public function isUsersExist(array $filters)
    {
        $selection = $this->provider->selectAll($filters);
        $existing = $this->provider->query($selection)->getResult();
        
        if (count($existing))
        {
            foreach ($existing as $exist)
            {
                if ($exist->getId())
                {
                    return true;
                }
            }
        }
        return false;
    }

    public function index($page=1, array $filters=array())
    {
        $selection = $this->provider->selectAll($filters);
        $paginator = $this->provider->getPaginator($selection);
        
        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage(20);
        
        return $paginator;
    }

    public function read($id)
    {
        $entity = $this->provider->findById($id);
        return $entity;
    }

    public function save(array $data)
    {
        if (isset($data['id']) && $data['id'])
        {
            $entity = $this->provider->findById($data['id']);
        }
        $entity->exchangeArray($data);

        if (isset($data['role']) && !empty($data['role'])) {
            $role = $this->getServiceManager()->get('doctrine.entitymanager.orm_default')->getReference('ZfMuscle\Entity\Role', $data['role']);
            $entity->emptyRoles();
            $entity->addRole($role);
        }
        $this->provider->save($entity);
        
        return $entity;
    }

    public function delete($id)
    {
        return $this->provider->delete($id);
    }

    public function setProvider(ZfMuscleAbstractProvider $provider)
    {
        $this->provider = $provider;
    }
}
