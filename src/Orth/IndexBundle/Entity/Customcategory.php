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
    private $parentRef;

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
     * Set parentRef
     *
     * @param integer $parentRef
     * @return Customcategory
     */
    public function setParentRef($parentRef)
    {
        $this->parentRef = $parentRef;

        return $this;
    }

    /**
     * Get parentRef
     *
     * @return integer 
     */
    public function getParentRef()
    {
        return $this->parentRef;
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
}
