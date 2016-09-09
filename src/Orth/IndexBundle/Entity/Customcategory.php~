<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customcategory
 */
class Customcategory
{
    /**
     * @var string
     */
    private $categoryName;

    /**
     * @var integer
     */
    private $checkedCat;

    /**
     * @var integer
     */
    private $customerRef;

    /**
     * @var integer
     */
    private $userRef;

    /**
     * @var integer
     */
    private $parentId;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set categoryName
     *
     * @param string $categoryName
     * @return Customcategory
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    /**
     * Get categoryName
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * Set customerRef
     *
     * @param integer $customerRef
     * @return Customcategory
     */
    public function setCustomerRef($customerRef)
    {
        $this->customerRef = $customerRef;

        return $this;
    }

    /**
     * Get customerRef
     *
     * @return integer
     */
    public function getCustomerRef()
    {
        return $this->customerRef;
    }

    /**
     * Set userRef
     *
     * @param integer $userRef
     * @return Customcategory
     */
    public function setUserRef($userRef)
    {
        $this->userRef = $userRef;

        return $this;
    }

    /**
     * Get userRef
     *
     * @return integer
     */
    public function getUserRef()
    {
        return $this->userRef;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return Customcategory
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $perm;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->perm = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add perm
     *
     * @param \Orth\IndexBundle\Entity\UserPermissions $perm
     * @return Customcategory
     */
    public function addPerm(\Orth\IndexBundle\Entity\UserPermissions $perm)
    {
        $this->perm[] = $perm;

        return $this;
    }

    /**
     * Remove perm
     *
     * @param \Orth\IndexBundle\Entity\UserPermissions $perm
     */
    public function removePerm(\Orth\IndexBundle\Entity\UserPermissions $perm)
    {
        $this->perm->removeElement($perm);
    }

    /**
     * Get perm
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPerm()
    {
        return $this->perm;
    }

    /**
     * Set checkedCat
     *
     * @param string $checkedCat
     * @return Customcategory
     */
    public function setCheckedCat($checkedCat)
    {
        $this->checkedCat = $checkedCat;

        return $this;
    }

    /**
     * Get checkedCat
     *
     * @return string
     */
    public function getCheckedCat()
    {
        return $this->checkedCat;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $customdata;


    /**
     * Add customdata
     *
     * @param \Orth\IndexBundle\Entity\Customerdata $customdata
     * @return Customcategory
     */
    public function addCustomdatum(\Orth\IndexBundle\Entity\Customerdata $customdata)
    {
        $this->customdata[] = $customdata;

        return $this;
    }

    /**
     * Remove customdata
     *
     * @param \Orth\IndexBundle\Entity\Customerdata $customdata
     */
    public function removeCustomdatum(\Orth\IndexBundle\Entity\Customerdata $customdata)
    {
        $this->customdata->removeElement($customdata);
    }

    /**
     * Get customdata
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomdata()
    {
        return $this->customdata;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Orth\IndexBundle\Entity\Customcategory
     */
    private $parent;

    /**
     * Add children
     *
     * @param \Orth\IndexBundle\Entity\Customcategory $children
     * @return Customcategory
     */
    public function addChild(\Orth\IndexBundle\Entity\Customcategory $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Orth\IndexBundle\Entity\Customcategory $children
     */
    public function removeChild(\Orth\IndexBundle\Entity\Customcategory $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Orth\IndexBundle\Entity\Customcategory $parent
     * @return Customcategory
     */
    public function setParent(\Orth\IndexBundle\Entity\Customcategory $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Orth\IndexBundle\Entity\Customcategory
     */
    public function getParent()
    {
        return $this->parent;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
}
