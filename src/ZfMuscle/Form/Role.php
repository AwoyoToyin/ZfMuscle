<?php

namespace ZfMuscle\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Doctrine\ORM\EntityManager;

/**
 * Description of Role
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class Role extends Form implements InputFilterProviderInterface {
    
    /**
     * @var EntityManager
     */
    protected $em;
    
    public function __construct(EntityManager $em) {
        parent::__construct();
        
        $this->em = $em;
        
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ))->add(array(
            'name'      => 'name',
            'options'   => array(
                'label' => 'Role Name',
            ),
            'attributes' => array(
                'type'          => 'text',
                'placeholder'   => 'Role Name',
                'class'         => 'form-control',
                'id'            => 'name',
                'autofocus'     => 'autofocus',
            ),
        ))->add(array(
            'type'    => 'DoctrineModule\Form\Element\ObjectSelect',
            'name'    => 'parent',
            'options' => array(
                'label'          => 'Parent Role',
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
                'data-placeholder'  => 'Select Parent Role',
                'data-init-plugin'  => 'select2',
                'id'                => 'parent',
            ),
        ));
        
        $submitElement = new Element\Button('submit');
        $submitElement
            ->setName('submit')
            ->setLabel('Save Role')
            ->setAttributes(array(
                'type'  => 'submit',
                'class'         => 'btn btn-primary btn-cons m-t-10',
                'id'            => 'addrole',
            ));

        $this->add($submitElement, array(
            'priority' => -100,
        ));
    }

    public function getInputFilterSpecification() {
        return array(
            'name' => array(
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
            'parent' => array(
                'required' => false,
            ),
        );
    }

}
