<?php

namespace ZfMuscle\Service;

use Zend\Cache\Storage\StorageInterface;

/**
 * Description of CacheService
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class CacheService
{
    protected $cache;
    
    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
    }
    
    public function getCache()
    {
        return $this->cache;
    }
}
