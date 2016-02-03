<?php

namespace ZfMuscle\Provider\Resource;

use Doctrine\Common\Persistence\ObjectRepository;
use BjyAuthorize\Provider\Resource\ProviderInterface;

/**
 * Guard provider based on a {@see \Doctrine\Common\Persistence\ObjectRepository}
 */
class DoctrineResourceProvider implements ProviderInterface {

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $objectRepository;

    /**
     * @var \Doctrine\ORM\EntityManaager
     */
    protected $objectManager;

    /**
     * @param \Doctrine\Common\Persistence\ObjectRepository $objectRepository            
     */
    public function __construct(ObjectRepository $objectRepository, $objectManager) {
        $this->objectRepository = $objectRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * Here we read rules from DB and put them into a form that BjyAuthorize's Controller.php understands
     * @return \Zend\Permissions\Acl\Resource\ResourceInterface[]
     */
    public function getResources()
    {
        $resources = array();
        // get the doctrine shemaManager
        $schemaManager = $this->objectManager->getConnection()->getSchemaManager();
        
        // check if the roles table exists, if it does not, do not bother querying
        if ($schemaManager->tablesExist(array('object_repository_controllers')) === true)
        {
            //read from object store a set of (controller)
            $result = $this->objectRepository->findAll();
            
            // if a result set exists
            if (count($result))
            {
                //transform to object BjyAuthorize will understand
                foreach ($result as $resource)
                {
                    // if controller contains '-', replace with nothing and set next character to uppercase
//                    $alias = $resource->getAlias();
//                    if (strpos($alias, '-') !== FALSE)
//                    {
//                        $stringArray = explode('-', $alias);
//                        foreach ($stringArray as $key => $value)
//                        {
//                            $stringArray[$key] = ucfirst($value);
//                        }
//                        $alias = implode('', $stringArray);
//                    }
//                    else
//                    {
//                        $alias = ucfirst($resource->getAlias());
//                    }
                    $resources[$resource->getAlias()] = array();
                }
            }
        }
        return $resources;
    }

}
