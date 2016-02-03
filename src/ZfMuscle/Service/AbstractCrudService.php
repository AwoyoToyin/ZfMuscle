<?php

namespace ZfMuscle\Service;

use Zend\ServiceManager\ServiceManager;
use ZfMuscle\Provider\ZfMuscleAbstractProvider;
use ZfcBase\EventManager\EventProvider;
use Zend\Stdlib\Hydrator;
use ZfMuscle\Service\ZfMuscleServiceInterface;

/**
 * Description of AbstractCrudService
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
abstract class AbstractCrudService extends EventProvider implements ZfMuscleServiceInterface
{
    /**
     * @var ZfMuscleAbstractProvider
     */
    protected $provider;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    
    /**
     * @var Hydrator\ClassMethods
     */
    protected $formHydrator;
    
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
            $entity = $this->read($data['id']);
        }
        else
        {
            $entity = $this->provider->createEntity();
        }
        
        return $entity;
    }

    public function delete($id)
    {
        try
        {
            return $this->provider->delete($id);
        }
        catch(\PDOException $ex)
        {
            return false;
        }
        catch(\Exception $ex)
        {
            return false;
        }
    }
    
    public function getEntityClass()
    {
        return $this->provider->createEntity();
    }

    public function getCrudForm($form)
    {
        return $this->getServiceManager()->get($form);
    }

    public function setProvider(ZfMuscleAbstractProvider $provider)
    {
        $this->provider = $provider;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Return the Form Hydrator
     *
     * @return \Zend\Stdlib\Hydrator\ClassMethods
     */
    public function getFormHydrator()
    {
        if (!$this->formHydrator instanceof Hydrator\HydratorInterface) {
            $this->setFormHydrator($this->getServiceManager()->get('default_hydrator'));
        }

        return $this->formHydrator;
    }

    /**
     * Set the Form Hydrator to use
     *
     * @param Hydrator\HydratorInterface $formHydrator
     * @return User
     */
    public function setFormHydrator(Hydrator\HydratorInterface $formHydrator)
    {
        $this->formHydrator = $formHydrator;
        return $this;
    }
    
    public function getDefaultAdminRole()
    {
        /**
         * check if an admin role has been defined in the config
         * if it has, use it for search
         * else, use the default 'administrator'
         */
        $config = $this->getServiceManager()->get('config');
        $adminRole = $config['zfmuscle']['default_admin_role'];
        $filters['name'] = array(
            'strategy'  => 'Equals',
            'value'     => $adminRole,
        );
        
        $selection = $this->provider->selectAll($filters);
        $result = $this->provider->query($selection)->getResult();
        
        if (count($result))
        {
            foreach ($result as $entity)
            {
                if ($entity->getId())
                {
                    return $entity->getId();
                }
            }
        }
        return false;
    }
}
