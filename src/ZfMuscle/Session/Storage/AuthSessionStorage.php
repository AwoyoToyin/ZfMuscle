<?php
/**
 * Created by PhpStorm.
 * User: Toyin
 * Date: 2/16/2016
 * Time: 1:17 PM
 */

namespace ZfMuscle\Session\Storage;

use Zend\Authentication\Storage\Session;
use Zend\Session\Container;

class AuthSessionStorage extends Session
{
    const SESSION_CONTAINER_NAME        = 'authContainer';
    const SESSION_VARIABLE_NAME         = 'authContainerVariable';

    private $allowedIdleTimeInSeconds   = 1800;

    public function setAuthenticationExpirationTime()
    {
        $expirationTime = time() + $this->allowedIdleTimeInSeconds;

        $authSession = new Container(self::SESSION_CONTAINER_NAME);

        if ($authSession->offsetExists(self::SESSION_VARIABLE_NAME)) {
            $authSession->offsetUnset(self::SESSION_VARIABLE_NAME);
        }

        $authSession->offsetSet(self::SESSION_VARIABLE_NAME, $expirationTime);
    }

    public function isExpiredAuthenticationTime()
    {
        $authSession = new Container(self::SESSION_CONTAINER_NAME);

        if ($authSession->offsetExists(self::SESSION_VARIABLE_NAME)) {
            $expirationTime = $authSession->offsetGet(self::SESSION_VARIABLE_NAME);
            return $expirationTime < time();
        }
        return false;
    }

    public function clearAuthenticationExpirationTime()
    {
        $authSession = new Container(self::SESSION_CONTAINER_NAME);
        $authSession->offsetUnset(self::SESSION_VARIABLE_NAME);
    }

    public function getAuthenticationExpirationTime()
    {
        $authSession = new Container(self::SESSION_CONTAINER_NAME);
        return $authSession->offsetGet(self::SESSION_VARIABLE_NAME);
    }

    /**
     * @param int $allowedIdleTimeInSeconds
     */
    public function setAllowedIdleTimeInSeconds($allowedIdleTimeInSeconds)
    {
        $this->allowedIdleTimeInSeconds = $allowedIdleTimeInSeconds;
    }
}