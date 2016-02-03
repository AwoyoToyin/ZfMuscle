<?php

namespace ZfMuscle\Repository;
 
use Doctrine\ORM\EntityRepository;

/**
 * Description of RolesRepository
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class RolesRepository extends EntityRepository
{
    public function getRoles()
    {
        $querybuilder = $this->createQueryBuilder('r');
        return $querybuilder->select('r')
                ->orderBy('r.id', 'ASC')
                ->getQuery()->getResult();
    }
}