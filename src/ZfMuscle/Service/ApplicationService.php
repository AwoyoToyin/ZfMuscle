<?php

namespace ZfMuscle\Service;

use ZfMuscle\Service\AbstractCrudService;
use Zend\Config\Config;
use Zend\Config\Writer\Xml;
use Zend\Config\Reader\Xml as Reader;

/**
 * Description of ApplicationService
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class ApplicationService extends AbstractCrudService
{
    const VALID_INSTALL_VALUE = "1";

    protected $_xml_install_path;

    protected $_resources = [];

    /**
     * Checks if the application needs to be installed
     * @return Boolean
     */
    public function isInstalled()
    {
        if (!file_exists($this->_getXmlInstallPath())) // if local.xml file does not exist
        {
            return false;
        }

        if(!$this->_isInstallationValid())
        {
            return false;
        }

        return $this->_isAdminUserExists();
    }

    /**
     * Checks if installation is valid
     * @return bool
     */
    protected function _isInstallationValid()
    {
        $reader = new Reader();
        $data   = $reader->fromFile($this->_getXmlInstallPath());
        $_isValid = $data['global']['install']['is_valid'];

        if ($_isValid !== static::VALID_INSTALL_VALUE)
        {
            return false;
        }
        $fileDbParams = $data['global']['install']['db_params'];
        if (($fileDbParams !== $this->_getDbParams()) === true)
        {
            return false;
        }
        return true;
    }

    /**
     * Checks if Admin User Exists
     * @return bool
     */
    protected function _isAdminUserExists()
    {
        /**
         * If no administrative user exists in the database,
         * then redirect to register page for a first time run
         */
        $users = $this->_getUsers([]);
        if (!$users)
        {
            return false;
        }
        return true;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    protected function _getUsers(array $filters)
    {
        $service = $this->_getService('zfmuscle_user_service');
        $users = $service->isUsersExist($filters);
        return $users;
    }

    /**
     * Starts Application Installation Process
     */
    public function run()
    {
        /**
         * Since it is assumed that this is our installation,
         * We need to insert our default roles in the database
         * since who ever installs this app is considered an administrator
         */
        $roleService = $this->_getService('zfmuscle_role_service');
        $roles = $this->_getDefaultRoles();

        // if the returned value is an array and it isn't empty
        if (is_array($roles) && $roles)
        {
            try
            {
                $lastId = '';
                foreach ($roles as $role)
                {
                    $lastId = $roleService->save($role, true, $lastId)->getId();
                }
                $this->_setAdminPermission();
            }
            catch (Exception $ex)
            {
                var_dump($ex); die;
            }
        }
    }

    public function _setAdminPermission()
    {
        $roleService = $this->_getService('zfmuscle_role_service');
        // fetch the admin role from the database for admin rule insertion
        $adminRoleId = $roleService->getDefaultAdminRole();
        if ($adminRoleId) {
            $service = $this->getServiceManager()->get('ZfMuscle\Service\RoleResource');
            $this->_resources = $service->fetchAllRoutes();
            $resourceService = $this->_getService('zfmuscle_resource_service');
            foreach ($this->_resources as $routes) {
                $data = [
                    'role_id' => $adminRoleId,
                    'resource_id' => $routes['route'],
                    'controller_id' => $routes['controller']
                ];
                $resourceService->save($data);
                if (isset($routes['children'])) {
                    foreach ($routes['children'] as $child) {
                        if (is_array($child) && !empty($child)) {
                            $data = [
                                'role_id' => $adminRoleId,
                                'resource_id' => $child['route'],
                                'controller_id' => $child['controller']
                            ];
                            $resourceService->save($data);
                        }
                    }
                }
            }
        }
    }

    public function generateXml()
    {
        try
        {
            if (file_exists($this->_getXmlInstallPath())) // if local.xml file exists, delete it
            {
                unlink($this->_getXmlInstallPath());
            }

            $dateTime = get_object_vars(new \DateTime());
            $array = [
                'global' => [
                    'install' => [
                        'is_valid' => true,
                        'db_params' => $this->_getDbParams(),
                        'date' => $dateTime['date']
                    ],
                ],

            ];
            $config = new Config($array, true);

            $writer = new Xml();
            $writer->toFile($this->_getXmlInstallPath(), $config);
        }
        catch (Exception $ex)
        {
            var_dump($ex); die;
        }
    }

    public function setXmlInstallPath($path)
    {
        $this->_xml_install_path = $path;
    }

    private function _getXmlInstallPath()
    {
        return $this->_xml_install_path;
    }

    private function _getService($instance)
    {
        return $this->getServiceManager()->get($instance);
    }

    protected function _getDbParams()
    {
        $params = [];
        $config = $this->_getService('config');
        if ($config['doctrine']['connection']['orm_default']['params'])
        {
            $raw = $config['doctrine']['connection']['orm_default']['params'];
            foreach ($raw as $key => $value)
            {
                if ($key === "driverOptions") { continue; }
                if ($key === "port") { $value = strval($value); }
                $params[$key] = $value;
            }
        }
        return $params;
    }

    /**
     * Returns ZfMuscle default roles for insert
     * @var type array
     */
    private function _getDefaultRoles()
    {
        $config = $this->_getService('config');
        $roles = $config['zfmuscle']['default_roles'];
        return $roles;
    }
}
