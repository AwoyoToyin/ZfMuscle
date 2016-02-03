<?php

namespace ZfMuscle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ZfMuscle\Entity\ZfMuscleEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Resource
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 * 
 * @ORM\Entity
 * @ORM\Table(name="admin_rule")
 */
class Resource implements ZfMuscleEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var Id
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var Resource ID
     */
    protected $resource_id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var Controller ID
     */
    protected $controller_id;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="resources")
     * @var Roles
     */
    protected $role;

    /**
     * Returns ResourceId
     * @return Object Id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * A method to set a controller
     * @param Role $role
     * @return Object Role
     */
    public function setRole(Role $role = null) {
        if ($this->role !== null) {
            $this->role->removeResource($this);
        }

        if ($role !== null) {
            $role->addResource($this);
        }
        $this->role = $role;
        return $this;
    }

    /**
     * A method to get a role
     * @return Object $role
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * A method to set a resource_id
     * @param string $resourceId
     * @return Object $resourceId
     */
    public function setResourceId($resourceId) {
        $this->resource_id = $resourceId;
        return $this;
    }

    /**
     * A method to get a resource_id
     * @return Object $resourceId
     */
    public function getResourceId() {
        return $this->resource_id;
    }

    /**
     * A method to set a ControllerId
     * @param string $controllerId
     * @return Object $controllerId
     */
    public function setControllerId($controllerId) {
        $this->controller_id = $controllerId;
        return $this;
    }

    /**
     * A method to get a ControllerId
     * @return Object $controllerId
     */
    public function getControllerId() {
        return $this->controller_id;
    }

    /**
     * @param array $data
     */
    public function exchangeArray(array $data) {
        foreach ($data as $key => $value) {
            $this->{$key} = (!empty($value)) ? $value : null;
        }
    }
}