<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderPositions
 */
class OrderPositions
{
    /**
     * @var integer
     */
    private $orderRef;

    /**
     * @var integer
     */
    private $varRef;

    /**
     * @var float
     */
    private $price;

    /**
     * @var integer
     */
    private $amount;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var text
     */
    private $positionsText;
    
    /**
     * Set orderRef
     *
     * @param integer $orderRef
     * @return OrderPositions
     */
    public function setOrderRef($orderRef)
    {
        $this->orderRef = $orderRef;

        return $this;
    }

    /**
     * Get orderRef
     *
     * @return integer 
     */
    public function getOrderRef()
    {
        return $this->orderRef;
    }

    /**
     * Set varRef
     *
     * @param integer $varRef
     * @return OrderPositions
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
     * Set price
     *
     * @param float $price
     * @return OrderPositions
     */
    public function setPrice($posPrice)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return OrderPositions
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
     * @var \Orth\IndexBundle\Entity\Orders
     */
    private $orders;


    /**
     * Set orders
     *
     * @param \Orth\IndexBundle\Entity\Orders $orders
     * @return OrderPositions
     */
    public function setOrders(\Orth\IndexBundle\Entity\Orders $orders = null)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Get orders
     *
     * @return \Orth\IndexBundle\Entity\Orders 
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
