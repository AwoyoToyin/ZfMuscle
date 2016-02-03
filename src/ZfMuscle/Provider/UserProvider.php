<?php

namespace ZfMuscle\Provider;

use ZfMuscle\Provider\AbstractDoctrineProvider;

/**
 * Description of UserProvider
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class UserProvider extends AbstractDoctrineProvider
{
    protected $entityClass = 'ZfMuscle\Entity\User';
}
