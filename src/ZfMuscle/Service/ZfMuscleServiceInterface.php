<?php

namespace ZfMuscle\Service;

use Zend\ServiceManager\ServiceManager;
use ZfMuscle\Provider\ZfMuscleAbstractProvider;

/**
 * Description of ZfMuscleServiceInterface
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
interface ZfMuscleServiceInterface {
    
    public function index();
    
    public function read($id);

    public function save(array $data);
    
    public function delete($id);
    
    public function setProvider(ZfMuscleAbstractProvider $model);

    public function setServiceManager(ServiceManager $serviceManager);
    
    public function getServiceManager();
}
