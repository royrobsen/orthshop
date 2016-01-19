<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Users
 */
class Users implements UserInterface, \Serializable
{
    /**
     * @var integer
     */
    private $customerRef;

    /**
     * @var integer
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var integer
     */
    private $userGroup;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $passkey;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set customerRef
     *
     * @param integer $customerRef
     * @return Users
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
     * Set firstName
     *
     * @param integer $firstName
     * @return Users
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return integer 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Users
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set userGroup
     *
     * @param integer $userGroup
     * @return Users
     */
    public function setUserGroup($userGroup)
    {
        $this->userGroup = $userGroup;

        return $this;
    }

    /**
     * Get userGroup
     *
     * @return integer 
     */
    public function getUserGroup()
    {
        return $this->userGroup;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Set passkey
     *
     * @param string $passkey
     * @return Users
     */
    public function setPasskey($passkey)
    {
        $this->passkey = $passkey;

        return $this;
    }

    /**
     * Get passkey
     *
     * @return string 
     */
    public function getPasskey()
    {
        return $this->passkey;
    }

        /**
     * Constructor
     */
    public function __construct()
    {
        $this->article = new \Doctrine\Common\Collections\ArrayCollection();
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
    
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }
    public function getPassword()
    {
        return $this->passkey;
    }
    
    public function getRoles()
    {
        if($this->userGroup == 1) {
            return array('ROLE_ADMIN');
        } 
        elseif($this->userGroup == 2) {
            return array('ROLE_USER');
        }
        elseif($this->userGroup == 3) {
            return array('ROLE_MODERATOR');
        }
        elseif($this->userGroup == 4) {
            return array('ROLE_BCUSTOMER');
        }
    }
    public function eraseCredentials()
    {
    }  
    
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->passkey,
            // see section on salt below
            // $this->salt,
        ));
    }
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->passkey,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }
}
