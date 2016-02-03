<?php

namespace ZfMuscle\Provider;

use ZfMuscle\Entity\ZfMuscleEntityInterface;
use ZfMuscle\Provider\ProviderInterface;
use Zend\Paginator\Paginator;

/**
 * Description of ZfMuscleAbstractProvider
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
abstract class ZfMuscleAbstractProvider implements ProviderInterface
{
    /**
     * Provides all entities
     */
    public function fetchAll()
    {
        $selection = $this->selectAll();
        $all = $this->query($selection);
        return $all;
    }
    
    /**
     * Creates a paginator for paged content
     *
     * @param unknown $selection
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator($selection)
    {
        $adapter = $this->getPaginatorAdapter($selection);
        $paginator = new Paginator($adapter);
        return $paginator;
    }
}
