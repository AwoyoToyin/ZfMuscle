<?php

namespace ZfMuscle\Provider\Resource;

use Doctrine\Common\Persistence\ObjectRepository;
use ZfMuscle\Service\ResourceService;
use BjyAuthorize\Provider\Resource\ProviderInterface;

/**
 * Guard provider based on a {@see \Doctrine\Common\Persistence\ObjectRepository}
 */
class DoctrineResourceProvider implements ProviderInterface {

    /**
     * @var \ZfMuscle\Service\ResourceService
     */
    protected $service;

    /**
     * @var \Doctrine\ORM\EntityManaager
     */
    protected $objectManager;

    /**
     * @param \ZfMuscle\Service\ResourceService $service
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function __construct(ResourceService $service, $objectManager) {
        $this->service = $service;
        $this->objectManager = $objectManager;
    }

    /**
     * Here we read rules from DB and put them into a form that BjyAuthorize's Controller.php understands
     * @return \Zend\Permissions\Acl\Resource\ResourceInterface[]
     */
    public function getResources()
    {
        $resources = [];
        // get the doctrine shemaManager
        $schemaManager = $this->objectManager->getConnection()->getSchemaManager();
        
        // check if the roles table exists, if it does not, do not bother querying
        if ($schemaManager->tablesExist(['admin_rule']) === true)
        {
            //read from object store a set of (controller)
            $result = $this->service->fetchAllResources([], [], ['controller_id']);
            // if a result set exists
            if (count($result))
            {
                //transform to object BjyAuthorize will understand
                foreach ($result as $resource)
                {
                    $resources[$resource->getControllerId()] = [];
                }
            }
        }
        return $resources;
    }

}
