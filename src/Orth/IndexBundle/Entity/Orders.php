<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orders
 */
class Orders
{
    /**
     * @var integer
     */
    private $invoiceAdrRef;

    /**
     * @var integer
     */
    private $shippingAdrRef;

    /**
     * @var integer
     */
    private $paymentMethod;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set invoiceAdrRef
     *
     * @param integer $invoiceAdrRef
     * @return Orders
     */
    public function setInvoiceAdrRef($invoiceAdrRef)
    {
        $this->invoiceAdrRef = $invoiceAdrRef;

        return $this;
    }

    /**
     * Get invoiceAdrRef
     *
     * @return integer 
     */
    public function getInvoiceAdrRef()
    {
        return $this->invoiceAdrRef;
    }

    /**
     * Set shippingAdrRef
     *
     * @param integer $shippingAdrRef
     * @return Orders
     */
    public function setShippingAdrRef($shippingAdrRef)
    {
        $this->shippingAdrRef = $shippingAdrRef;

        return $this;
    }

    /**
     * Get shippingAdrRef
     *
     * @return integer 
     */
    public function getShippingAdrRef()
    {
        return $this->shippingAdrRef;
    }

    /**
     * Set paymentMethod
     *
     * @param integer $paymentMethod
     * @return Orders
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return integer 
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Orders
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
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
    private $deliveryMethod;


    /**
     * Set deliveryMethod
     *
     * @param integer $deliveryMethod
     * @return Orders
     */
    public function setDeliveryMethod($deliveryMethod)
    {
        $this->deliveryMethod = $deliveryMethod;

        return $this;
    }

    /**
     * Get deliveryMethod
     *
     * @return integer 
     */
    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
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
     * @var \DateTime
     */
    private $createdDate;


    /**
     * Set userRef
     *
     * @param integer $userRef
     * @return Orders
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
     * @return Orders
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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Orders
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $positions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->positions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add positions
     *
     * @param \Orth\IndexBundle\Entity\OrderPositions $positions
     * @return Orders
     */
    public function addPosition(\Orth\IndexBundle\Entity\OrderPositions $positions)
    {
        $this->positions[] = $positions;

        return $this;
    }

    /**
     * Remove positions
     *
     * @param \Orth\IndexBundle\Entity\OrderPositions $positions
     */
    public function removePosition(\Orth\IndexBundle\Entity\OrderPositions $positions)
    {
        $this->positions->removeElement($positions);
    }

    /**
     * Get positions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPositions()
    {
        return $this->positions;
    }
    /**
     * @var string
     */
    private $customerOrderNumber;


    /**
     * Set customerOrderNumber
     *
     * @param string $customerOrderNumber
     * @return Orders
     */
    public function setCustomerOrderNumber($customerOrderNumber)
    {
        $this->customerOrderNumber = $customerOrderNumber;

        return $this;
    }

    /**
     * Get customerOrderNumber
     *
     * @return string 
     */
    public function getCustomerOrderNumber()
    {
        return $this->customerOrderNumber;
    }
}
