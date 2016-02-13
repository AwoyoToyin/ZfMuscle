<?php

namespace ZfMuscle\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcUser\Options\AuthenticationOptionsInterface;
use ZfcUser\Form\Login as ZfcLoginForm;

/**
 * Description of Login
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class Login extends ZfcLoginForm
{
    /**
     * @var AuthenticationOptionsInterface
     */
    protected $authOptions;

    public function __construct($name, AuthenticationOptionsInterface $options)
    {
        $this->setUseInputFilterDefaults(false); // remove zf2 default validators
        $this->setAuthenticationOptions($options);
        parent::__construct($name, $options);
        
        $this->get('identity')
                ->setAttributes(array(
                    'placeholder'   => 'e.g example@gmail.com',
                    'class'         => 'form-control',
                    'id'            => 'identity',
                    'autofocus'     => 'autofocus',
                ));
        $this->get('credential')
                ->setAttributes(array(
                    'placeholder'   => 'Password',
                    'class'         => 'form-control',
                    'id'            => 'credential',
                ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'remember_me',
            'options' => array(
                'label' => 'Remember Me',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'class' => 'checkbox',
                'id' => 'remember_me',
            ),
        ));
        $this->get('submit')
                ->setName('login')
                ->setAttributes(array(
                    'class' => 'btn btn-sm btn-primary btn-block',
                    'id'    => 'login',
                ));
    }
}
