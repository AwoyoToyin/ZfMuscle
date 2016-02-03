<?php

namespace ZfMuscle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ZfMuscle\Entity\ZfMuscleEntityInterface;
use BjyAuthorize\Acl\HierarchicalRoleInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of UserRole
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 *
 * This class represents a UserRole item
 * 
 * @ORM\Table(name="roles")
 * @ORM\Entity(repositoryClass="\ZfMuscle\Repository\RolesRepository")
 */
class Role implements ZfMuscleEntityInterface, HierarchicalRoleInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="Role")
     */
    protected $parent;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Resource", mappedBy="role", cascade={"remove"})
     * @var Resources[]
     */
    protected $resources;

    public function __construct() {
        $this->resources = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    /**
     * Set the parent role.
     * @param Role $parent
     * @return void
     */
    public function setParent(Role $parent) {
        $this->parent = $parent;
    }

    /**
     * Get the parent role
     * @return Role
     */
    public function getParent() {
        return $this->parent;
    }

    public function getRoleId() {
        return $this->getName();
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function addResource(Resource $resource) {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
        }
        return $this;
    }

    public function removeResource(Resource $resource)
    {
        if (!$this->resources->contains($resource)) {
            return;
        }
        
        $this->resources->removeElement($resource);
        return $this;
    }

    public function getResources() {
        return $this->resources;
    }
    
    public function emptyResources() {
        $this->resources = new ArrayCollection();
    }

    public function exchangeArray(array $data) {
        foreach ($data as $key => $value) {
            $this->{$key} = (!empty($value)) ? $value : null;
        }
    }

}
