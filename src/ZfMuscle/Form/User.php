<?php

namespace ZfMuscle\Form;

use ZfcUser\Form\Register;
use Zend\Form\Element;
use ZfcUser\Options\RegistrationOptionsInterface;
use Doctrine\ORM\EntityManager;

/**
 * Description of User
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class User extends Register
{
    
    protected $em;
    
    public function __construct(EntityManager $em, $name = null, RegistrationOptionsInterface $options)
    {
        $this->setUseInputFilterDefaults(false); // remove zf2 default validators
        parent::__construct($name, $options);
        
        $this->em = $em;
        
//        $this->get('userId')
//                ->setName('user_id');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        
        $this->get('email')
                ->setLabel('Email Address')
                ->setAttributes(array(
                    'placeholder'   => 'We will be sending your login details here',
                    'class'         => 'form-control',
                    'id'            => 'email',
                ));
        $this->get('username')
                ->setAttributes(array(
                    'placeholder'   => 'Username (this can be changed later)',
                    'class'         => 'form-control',
                    'id'            => 'username',
                ));
        $this->get('password')
                ->setAttributes(array(
                    'placeholder'   => 'Minimum of 6 characters',
                    'class'         => 'form-control',
                    'id'            => 'password',
                ));
        $this->get('passwordVerify')
                ->setAttributes(array(
                    'placeholder'   => 'Must match the password',
                    'class'         => 'form-control',
                    'id'            => 'passwordVerify',
                ));
        $this->get('submit')
                ->setName('install')
                ->setLabel('Install')
                ->setAttributes(array(
                    'class'         => 'btn btn-primary btn-block',
                    'id'            => 'install',
                ));
        
        $this->add(array(
            'name'      => 'firstname',
            'options'   => array(
                'label' => 'First Name',
            ),
            'attributes' => array(
                'type'          => 'text',
                'placeholder'   => 'John',
                'class'         => 'form-control',
                'id'            => 'firstname',
                'autofocus'     => 'autofocus',
            ),
        ))->add(array(
            'name'      => 'lastname',
            'options'   => array(
                'label' => 'Last Name',
            ),
            'attributes' => array(
                'type'          => 'text',
                'placeholder'   => 'Doe',
                'class'         => 'form-control',
                'id'            => 'lastname',
            ),
        ))->add(array(
            'type'    => 'DoctrineModule\Form\Element\ObjectSelect',
            'name'    => 'role',
            'options' => array(
                'label'          => 'Role',
                'object_manager' => $this->em,
                'target_class'   => 'ZfMuscle\Entity\Role',
                'property'       => 'roleId',
                'empty_option'   => 'please select...',
                'is_method'      => true,
                'find_method'    => array(
                    'name'   => 'getRoles',
                ),
            ),
            'attributes' => array(
                'class'             => 'full-width',
                'data-placeholder'  => 'Select User Role',
                'data-init-plugin'  => 'select2',
                'id'                => 'role',
            ),
        ));
    }

}
