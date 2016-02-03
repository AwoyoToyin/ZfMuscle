<?php

namespace ZfMuscle\Provider;

use ZfMuscle\Provider\ZfMuscleAbstractProvider;
use ZfMuscle\Entity\ZfMuscleEntityInterface;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Doctrine\ORM\EntityManager;

/**
 * Description of ProviderInterface
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
abstract class AbstractDoctrineProvider extends ZfMuscleAbstractProvider
{
    /**
     * @var EntityManager
     */
    protected $em;
    
    /**
     * Name of Entity class
     *
     * @var string
     */
    protected $entityClass = null;
    
    /**
     * An alias to use in queries
     *
     * @var String
     */
    protected $entityAlias = 'entity';

    /**
     * Sets the instance of an EntityManager
     *
     * @param EntityManager $em
     * @return \ZfMuscle\Provider\ZfMuscleAbstractProvider
     */
    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
        return $this;
    }

    /**
     * Gets the instance of an EntityManager
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->em;
    }
    
    /**
     * (non-PHPdoc)
     * @see \ZfMuscle\Provider\ProviderInterface::selectAll()
     */
    public function selectAll(array $filters = array(), $orderBy=array()){
        $selection = $this->getEntityManager()->createQueryBuilder();
    
        $selection->select(array($this->entityAlias))
                ->from($this->entityClass, $this->entityAlias);
    
        if(!empty($filters)){
            $pc = 1;
            $params = array();
    
            foreach($filters as $field => $specs){
                switch ($specs['strategy']){
                    default:
                    case 'Equals':
                        $part = "{$this->entityAlias}.{$field} = ?{$pc}";
                        $params[$pc] = $specs['value'];
                        break;
                    case 'NotEquals':
                        $part = "{$this->entityAlias}.{$field} <> ?{$pc}";
                        $params[$pc] = $specs['value'];
                        break;
                    case 'Contains':
                        $part = "{$this->entityAlias}.{$field} Like ?{$pc}";
                        $params[$pc] = "%{$specs['value']}%";
                        break;
                    case 'StartsWith':
                        $part = "{$this->entityAlias}.{$field} Like ?{$pc}";
                        $params[$pc] = "%{$specs['value']}";
                        break;
                    case 'EndsWith':
                        $part = "{$this->entityAlias}.{$field} Like ?{$pc}";
                        $params[$pc] = "{$specs['value']}%";
                        break;
                }
    
                $selection->andWhere($part);
    
                $pc++;
            }
             
            $selection->setParameters($params);
        }
    
        if (!empty($orderBy))
        {
            foreach ($orderBy as $field => $dir){
                $selection->orderBy($field, $dir);
            }
        }
        
        return $selection;
    }
    
    /**
     * (non-PHPdoc)
     * @see \ZfMuscle\Provider\ProviderInterface::selectJoin()
     */
    public function selectJoin(array $filters = array(), $joins = array(), $orderBy=array())
    {
        try
        {
            $selection = $this->getEntityManager()->createQueryBuilder();

            $aliases = array();
            $aliases[] = $this->entityAlias;

            foreach ($joins as $table => $attributes)
            {
                $aliases[] = $attributes['alias'];
            }

            $selection->select($aliases)
                    ->from($this->entityClass, $this->entityAlias);

            foreach ($joins as $table => $attributes)
            {
                $selection->Join(
                        $table,
                        $attributes['alias'],
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        "{$attributes['main_table_field']} = {$attributes['alias']}.{$attributes['join_table_field']}");
            }
            
//            die($selection);

            if(!empty($filters))
            {
                $pc = 1;
                $params = array();

                foreach($filters as $field => $specs)
                {
                    switch ($specs['strategy'])
                    {
                        default:
                        case 'Equals':
                            $part = "{$this->entityAlias}.{$field} = ?{$pc}";
                            $params[$pc] = $specs['value'];
                            break;
                        case 'NotEquals':
                            $part = "{$this->entityAlias}.{$field} <> ?{$pc}";
                            $params[$pc] = $specs['value'];
                            break;
                        case 'Contains':
                            $part = "{$this->entityAlias}.{$field} Like ?{$pc}";
                            $params[$pc] = "%{$specs['value']}%";
                            break;
                        case 'StartsWith':
                            $part = "{$this->entityAlias}.{$field} Like ?{$pc}";
                            $params[$pc] = "%{$specs['value']}";
                            break;
                        case 'EndsWith':
                            $part = "{$this->entityAlias}.{$field} Like ?{$pc}";
                            $params[$pc] = "{$specs['value']}%";
                            break;
                    }

                    $selection->andWhere($part);

                    $pc++;
                }

                $selection->setParameters($params);
            }

            foreach ($orderBy as $field => $dir){
                $selection->orderBy($field, $dir);
            }

            return $selection;
        } catch (Exception $ex) {
            var_dump($ex->getMessages()); die;
        }
    }
    
    /**
     * Retrieves the entities based on the selection
     * 
     * @param unknown $selection
     */
    public function query($selection){
        return $selection->getQuery();
    }
    
    /**
     * Gets the adapter for \Zend\Paginator\Paginator
     * 
     * @param unknown $query
     */
    public function getPaginatorAdapter($selection){
        return new DoctrinePaginator(new ORMPaginator($selection));
    }

    /**
     * (non-PHPdoc)
     * @see \ZfMuscle\Provider\ProviderInterface::findById()
     */
    public function findById($id) {
        $entity = $this->getEntityManager()->getRepository($this->entityClass);
        return $entity->find($id);
    }

    /**
     * (non-PHPdoc)
     * @see \ZfMuscle\Provider\ProviderInterface::save()
     */
    public function save(ZfMuscleEntityInterface $entity) {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush($entity);
    }

    /**
     * (non-PHPdoc)
     * @see \ZfMuscle\Provider\ProviderInterface::createEntity()
     */
    public function createEntity() {
        $entity = new $this->entityClass();
        return $entity;
    }

    /**
     * (non-PHPdoc)
     * @see \ZfMuscle\Provider\ProviderInterface::delete()
     */
    public function delete($id) {
        $entity = $this->findById($id);
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
}