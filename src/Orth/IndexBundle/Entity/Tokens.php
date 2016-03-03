<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tokens
 */
class Tokens
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var \DateTime
     */
    private $expDate;

    /**
     * @var integer
     */
    private $userRef;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set token
     *
     * @param string $token
     * @return Tokens
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set expDate
     *
     * @param \DateTime $expDate
     * @return Tokens
     */
    public function setExpDate($expDate)
    {
        $this->expDate = $expDate;

        return $this;
    }

    /**
     * Get expDate
     *
     * @return \DateTime 
     */
    public function getExpDate()
    {
        return $this->expDate;
    }

    /**
     * Set userRef
     *
     * @param integer $userRef
     * @return Tokens
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var \Orth\IndexBundle\Entity\Users
     */
    private $user;


    /**
     * Set user
     *
     * @param \Orth\IndexBundle\Entity\Users $user
     * @return Tokens
     */
    public function setUser(\Orth\IndexBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Orth\IndexBundle\Entity\Users 
     */
    public function getUser()
    {
        return $this->user;
    }
}
