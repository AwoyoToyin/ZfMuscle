<?php

namespace ZfMuscle\Provider;

use ZfMuscle\Entity\ZfMuscleEntityInterface;

/**
 * Description of ProviderInterface
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
interface ProviderInterface
{
    function findById($id);
    
    function save(ZfMuscleEntityInterface $entity);
    
    function delete($id);
    
    /**
     * Creates an instance of the Enitity being provided
     */
    function createEntity();

    /**
     * Queries the datastore for entities
     *
     * @param Selection $selection
     */
    function query($selection);

    /**
     * Selects all entities from the repository
     *
     * @return Selection
     */
    function selectAll(array $filters = array());
    
    function selectJoin(array $filters = array(), $joins = array());
}