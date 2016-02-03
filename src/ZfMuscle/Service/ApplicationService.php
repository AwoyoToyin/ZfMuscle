<?php

namespace ZfMuscle\Service;

use ZfMuscle\Service\AbstractCrudService;

/**
 * Description of ApplicationService
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class ApplicationService extends AbstractCrudService
{
    /**
     * Checks if the application needs to be installed
     * @return Boolean
     */
    public function isInstalled()
    {
        $filters = array();
        $users = $this->_getUsers($filters);
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
            }
            catch (Exception $ex)
            {
                var_dump($ex); die;
            }
        }
    }

    private function _getUsers(array $filters)
    {
        $service = $this->_getUserService();
        $users = $service->isUsersExist($filters);
        return $users;
    }

    private function _getUserService()
    {
        return $this->getServiceManager()->get('zfmuscle_user_service');
    }

    private function _getRoleService()
    {
        return $this->getServiceManager()->get('zfmuscle_role_service');
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
