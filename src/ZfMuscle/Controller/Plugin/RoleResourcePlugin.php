<?php

namespace ZfMuscle\Controller\Plugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManager;

/**
 * Description of RoleResourcePlugin
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class RoleResourcePlugin extends AbstractPlugin
{
    /**
     * @var ServiceLocator
     */
    protected $serviceLocator;
    
    public function __construct(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        $mainModule = array();
        
        $moduleManager = $this->serviceLocator->get('ModuleManager');
        $loadedModules = array_keys($moduleManager->getLoadedModules());
        $loadedModules = array_diff($loadedModules, array(
                'ZendDeveloperTools', 'DoctrineModule', 'DoctrineORMModule', 'AssetManager', 'ZfcUserDoctrineORM', 'BjyAuthorize', 'ZfcBase', 'ZfcUser'
            ));
        
//        var_dump($loadedModules); die;
        $skipActionsList = array('notFoundAction', 'getMethodFromAction');

        foreach ($loadedModules as $loadedModule)
        {
            $moduleClass = '\\' .$loadedModule . '\Module';
            $moduleObject = new $moduleClass;
            
            if (!$moduleObject->getConfig())
            {
                continue;
            }
            
            $config = $moduleObject->getConfig();

            foreach ($config as $key => $value)
            {
                if ($key !== 'controllers')
                {
                    continue;
                }
                
                foreach ($config['controllers'] as $k => $v)
                {
                    if ($k !== 'invokables')
                    {
                        continue;
                    }
                    
                    $controllers = $config['controllers']['invokables'];
                    foreach ($controllers as $k => $moduleClass)
                    {
                        $tmpArray = get_class_methods($moduleClass);
                        $actions = array();
                        
                        if (!count($tmpArray))
                        {
                            continue;
                        }
                        
                        foreach ($tmpArray as $action) {
                            if (substr($action, strlen($action)-6) === 'Action' && !in_array($action, $skipActionsList)) {
                                $actions[] = ucfirst(substr($action, 0, -6));
                            }
                        }
                        
                        $mainModule[$loadedModule][$moduleClass] = array(
                            'alias'     => $k,
                            'actions'   => $actions,
                        );
                    }
                }
            }
        }
        return $mainModule;
    }
}