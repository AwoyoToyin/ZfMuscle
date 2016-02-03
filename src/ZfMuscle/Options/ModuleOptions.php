<?php

namespace ZfMuscle\Options;

use Zend\Stdlib\AbstractOptions;
use ZfcUserDoctrineORM\Options\ModuleOptions as ZfcUserDoctrineORMModuleOptions;

class ModuleOptions extends ZfcUserDoctrineORMModuleOptions
{
    /**
     * @var string
     */
    protected $loginRedirectRoute = 'zfmuscle';

    /**
     * @var string
     */
    protected $logoutRedirectRoute = 'zfmuscle/users/login';

    /**
     * @var int
     */
    protected $enableUserState = false;

    /**
     * @var int
     */
    protected $defaultUserState = 1;

    /**
     * @var array
     */
    protected $authIdentityFields = array( 'email' );

    /**
     * @var string
     */
    protected $userEntityClass = 'ZfcMuscle\Entity\User';

    /**
     * @var bool
     */
    protected $enableRegistration = true;

    /**
     * @var bool
     */
    protected $enableFirstname = true;

    /**
     * @var bool
     */
    protected $enableLastname = true;

    /**
     * @var bool
     */
    protected $enableUsername = false;

    /**
     * @var array
     */
    protected $formCaptchaOptions = array(
        'class'   => 'figlet',
        'options' => array(
            'wordLen'    => 5,
            'expiration' => 300,
            'timeout'    => 300,
        ),
    );

    /**
     * set enable firstname
     *
     * @param bool $flag
     * @return ModuleOptions
     */
    public function setEnableFirstname($flag)
    {
        $this->enableFirstname = (bool) $flag;
        return $this;
    }

    /**
     * get enable firstname
     *
     * @return bool
     */
    public function getEnableFirstname()
    {
        return $this->enableFirstname;
    }

    /**
     * set enable lastname
     *
     * @param bool $flag
     * @return ModuleOptions
     */
    public function setEnableLastname($flag)
    {
        $this->enableLastname = (bool) $flag;
        return $this;
    }

    /**
     * get enable firstname
     *
     * @return bool
     */
    public function getEnableLastname()
    {
        return $this->enableLastname;
    }
}
