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
     * @var text
     */
    private $positionsText;
    
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
     * Set positionsText
     *
     * @param integer $positionsText
     * @return OrderPositions
     */
    public function setPositionsText($positionsText)
    {
        $this->positionsText = $positionsText;

        return $this;
    }

    /**
     * Get positionsText
     *
     * @return integer 
     */
    public function getPositionsText()
    {
        return $this->positionsText;
    } 

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
    
    /**
     * @var integer
     */
    private $customdataRef;

    /**
     * @var integer
     */
    private $approvalRef;


    /**
     * Set customdataRef
     *
     * @param integer $customdataRef
     * @return ShoppingCart
     */
    public function setCustomdataRef($customdataRef)
    {
        $this->customdataRef = $customdataRef;

        return $this;
    }

    /**
     * Get customdataRef
     *
     * @return integer 
     */
    public function getCustomdataRef()
    {
        return $this->customdataRef;
    }

    /**
     * Set approvalRef
     *
     * @param integer $approvalRef
     * @return ShoppingCart
     */
    public function setApprovalRef($approvalRef)
    {
        $this->approvalRef = $approvalRef;

        return $this;
    }

    /**
     * Get approvalRef
     *
     * @return integer 
     */
    public function getApprovalRef()
    {
        return $this->approvalRef;
    }
}
