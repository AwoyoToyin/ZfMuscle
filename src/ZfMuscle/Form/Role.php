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
        
        $this->add([
            'name' => 'id',
            'attributes' => [
                'type' => 'hidden',
            ],
        ])->add([
            'name'      => 'name',
            'options'   => [
                'label' => 'Role Name',
            ],
            'attributes' => [
                'type'          => 'text',
                'placeholder'   => 'Role Name',
                'class'         => 'form-control',
                'id'            => 'name',
                'autofocus'     => 'autofocus',
            ],
        ])->add([
            'type'    => 'DoctrineModule\Form\Element\ObjectSelect',
            'name'    => 'parent',
            'options' => [
                'label'          => 'Parent Role',
                'object_manager' => $this->em,
                'target_class'   => 'ZfMuscle\Entity\Role',
                'property'       => 'roleId',
                'empty_option'   => '--Select--',
                'is_method'      => true,
                'find_method'    => [
                    'name'   => 'getRoles',
                ],
            ],
            'attributes' => [
                'class'             => 'full-width',
                'data-placeholder'  => 'Select Parent Role',
                'data-init-plugin'  => 'select2',
                'id'                => 'parent',
            ],
        ]);
        
        $submitElement = new Element\Button('submit');
        $submitElement
            ->setName('submit')
            ->setLabel('Save Role')
            ->setAttributes([
                'type'  => 'submit',
                'class'         => 'btn btn-primary btn-cons m-t-10',
                'id'            => 'addrole',
            ]);

        $this->add($submitElement, [
            'priority' => -100,
        ]);
    }

    public function getInputFilterSpecification() {
        return [
            'name' => [
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
            'parent' => [
                'required' => false,
            ],
        ];
    }

}
