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

    protected $config;
    
    public function __construct($config, ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        // get the module manager
        $moduleManager = $this->serviceLocator->get('ModuleManager');
        // get all loaded modules application wide
        $loadedModules = array_keys($moduleManager->getLoadedModules());
        // remove default unwanted modules
        $loadedModules = array_diff($loadedModules, $this->config['skip_modules']);

        // loop through all the loaded modules
        foreach ($loadedModules as $loadedModule)
        {
            $moduleClass = '\\' .$loadedModule . '\Module';
            $moduleObject = new $moduleClass;

            if (!$moduleObject->getConfig())
            {
                continue;
            }

            // get all routes for current module
            $module = $moduleManager->getModule($loadedModule);
            $routes = $module->getConfig()['router']['routes'];

            // if the routes variable (array) is not empty
            if (!empty($routes))
            {
                // loop through each route
                foreach ($routes as $key => $route)
                {
                    // check for route type for the root route and act accordingly
//                    $this->_setRootRoute($route, $key);

                    // loop the children routes and act accordingly
                    $this->_setResources($route, $key);
                }
            }
        }
        return $this->_resources;
    }

    /**
     * @param $route
     * @return array
     */
    private function _getSegmentedRoute($route)
    {
        if (isset($route['options']['defaults']) && !empty($route['options']['defaults']))
        {
            if (!isset($route['options']['constraints']) || empty($route['options']['constraints']))
            {
                $_controller = $route['options']['defaults']['controller'];
            } else {
                if (isset($route['options']['defaults']['__NAMESPACE__']) && !empty($route['options']['defaults']['__NAMESPACE__']))
                {
                    $_controller = $route['options']['defaults']['__NAMESPACE__'] . '\\' . $route['options']['defaults']['controller'];
                } else {
                    $_controller = $route['options']['defaults']['controller'];

                    if (substr($_controller, strlen($_controller)-10) === 'Controller')
                    {
                        $controllerConstraints = explode('\\', $route['options']['defaults']['controller']);
                        if (count($controllerConstraints) > 2)
                        {
                            $_controller = strtolower(str_replace("Controller", "", end($controllerConstraints)));
                        }
                    }
                }
            }
            return array($_controller);
        }
    }

    /**
     * @param $route
     * @return array
     */
    private function _getLiteralRoute($route)
    {
        if (isset($route['options']['defaults']) && !empty($route['options']['defaults']))
        {
            if (isset($route['options']['defaults']['__NAMESPACE__']) && !empty($route['options']['defaults']['__NAMESPACE__'])) {
                $_controller = $route['options']['defaults']['__NAMESPACE__'] . '\\' . $route['options']['defaults']['controller'];
            } else {
                $_controller = $route['options']['defaults']['controller'];
            }
            return array($_controller);
        }
    }

    /**
     * @param $route
     * @param $key
     * @param $childKey
     */
    private function _setChildRoutes($route, $key, $childKey = null)
    {
        $chldKey = $key;

        if (!is_null($childKey))
        {
            $chldKey = $key . '/' .$childKey;
        }

        if (isset($route['child_routes']) && !empty($route['child_routes']))
        {
            foreach ($route['child_routes'] as $childKey => $childRoute)
            {
                if ($childRoute['type'] === 'Literal' || $childRoute['type'] === 'Zend\Mvc\Router\Http\Literal')
                {
                    list($_controller) = $this->_getLiteralRoute($childRoute);
                    if ($_controller)
                    {
                        $this->_resources[$key]['children'][] = [
                            'route' => $chldKey . '/' .$childKey,
                            'controller' => $_controller,
                        ];
                    }
                }
                elseif ($childRoute['type'] === 'Segment' || $childRoute['type'] === 'Zend\Mvc\Router\Http\Segment')
                {
                    list($_controller) = $this->_getSegmentedRoute($childRoute);
                    $this->_resources[$key]['children'][] = [
                        'route' => $chldKey . '/' . $childKey,
                        'controller' => $_controller,
                    ];
                }
                if (isset($childRoute['child_routes']) && !empty($childRoute['child_routes']))
                {
                    $this->_setChildRoutes($childRoute, $key, $childKey);
                }
            }
        }
    }

    /**
     * @param $route
     * @param $key
     */
    private function _setResources($route, $key)
    {
        if ($route['type'] === 'Literal' || $route['type'] === 'Zend\Mvc\Router\Http\Literal')
        {
            list($_controller) = $this->_getLiteralRoute($route);
            $this->_resources[$key] = [
                'route' => $key,
                'controller' => $_controller,
            ];
        } else {
            if ($route['type'] === 'Segment' || $route['type'] === 'Zend\Mvc\Router\Http\Segment')
            {
                list($_controller) = $this->_getSegmentedRoute($route);
                $this->_resources[$key] = [
                    'route' => $key,
                    'controller' => $_controller,
                ];
            }
        }

        if (isset($route['child_routes']) && !empty($route['child_routes']))
        {
            $this->_setChildRoutes($route, $key);
        }
    }
}