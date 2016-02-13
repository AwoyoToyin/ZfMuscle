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
        return $this->_isInstallationValid();
    }

    protected function _isInstallationValid()
    {
        $reader = new Reader();
        $data   = $reader->fromFile($this->_getXmlInstallPath());
        $_isValid = $data['global']['install']['is_valid'];

        if ($_isValid === static::VALID_INSTALL_VALUE)
        {
            $fileDbParams = $data['global']['install']['db_params'];
            if (($fileDbParams === $this->_getDbParams()) === true)
            {
                return true;
            }
            return false;
        }
        return false;
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
        $roleService = $this->_getRoleService();
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
        $roleService = $this->_getRoleService();
        // fetch the admin role from the database for admin rule insertion
        $adminRoleId = $roleService->getDefaultAdminRole();
        if ($adminRoleId) {
            $service = $this->getServiceManager()->get('ZfMuscle\Service\RoleResource');
            $this->_resources = $service->fetchAllRoutes();
            $resourceService = $this->_getResourceService();
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

    private function _getRoleService()
    {
        return $this->getServiceManager()->get('zfmuscle_role_service');
    }

    private function _getResourceService()
    {
        return $this->getServiceManager()->get('zfmuscle_resource_service');
    }

    protected function _getDbParams()
    {
        $params = [];
        $config = $this->getServiceManager()->get('config');
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
        $config = $this->getServiceManager()->get('config');
        $roles = $config['zfmuscle']['default_roles'];
        return $roles;
    }
}
