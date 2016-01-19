<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPermissions
 */
class UserPermissions
{
    /**
     * @var integer
     */
    private $userRef;

    /**
     * @var integer
     */
    private $custcatRef;

    /**
     * @var integer
     */
    private $permStatus;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set userRef
     *
     * @param integer $userRef
     * @return UserPermissions
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
     * Set custcatRef
     *
     * @param integer $custcatRef
     * @return UserPermissions
     */
    public function setCustcatRef($custcatRef)
    {
        $this->custcatRef = $custcatRef;

        return $this;
    }

    /**
     * Get custcatRef
     *
     * @return integer 
     */
    public function getCustcatRef()
    {
        return $this->custcatRef;
    }

    /**
     * Set permStatus
     *
     * @param integer $permStatus
     * @return UserPermissions
     */
    public function setPermStatus($permStatus)
    {
        $this->permStatus = $permStatus;

        return $this;
    }

    /**
     * Get permStatus
     *
     * @return integer 
     */
    public function getPermStatus()
    {
        return $this->permStatus;
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
     * @var \Orth\IndexBundle\Entity\Customcategory
     */
    private $custcat;


    /**
     * Set custcat
     *
     * @param \Orth\IndexBundle\Entity\Customcategory $custcat
     * @return UserPermissions
     */
    public function setCustcat(\Orth\IndexBundle\Entity\Customcategory $custcat = null)
    {
        $this->custcat = $custcat;

        return $this;
    }

    /**
     * Get custcat
     *
     * @return \Orth\IndexBundle\Entity\Customcategory 
     */
    public function getCustcat()
    {
        return $this->custcat;
    }
}
