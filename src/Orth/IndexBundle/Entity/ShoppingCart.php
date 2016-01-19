<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShoppingCart
 */
class ShoppingCart
{
    /**
     * @var integer
     */
    private $varRef;

    /**
     * @var integer
     */
    private $amount;

    /**
     * @var string
     */
    private $sessionId;
    
    /**
     * @var integer
     */
    private $id;


    /**
     * Set varRef
     *
     * @param integer $varRef
     * @return ShoppingCart
     */
    public function setVarRef($varRef)
    {
        $this->varRef = $varRef;

        return $this;
    }

    /**
     * Get varRef
     *
     * @return integer 
     */
    public function getVarRef()
    {
        return $this->varRef;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return ShoppingCart
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @var integer
     */
    private $userRef;

    /**
     * @var integer
     */
    private $customerRef;


    /**
     * Set userRef
     *
     * @param integer $userRef
     * @return ShoppingCart
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
     * Set customerRef
     *
     * @param integer $customerRef
     * @return ShoppingCart
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
     * Set sessionId
     *
     * @param string $varRef
     * @return ShoppingCart
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
    
}
