<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pricesets
 */
class Pricesets
{
    /**
     * @var integer
     */
    private $customerRef;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $pricesetdata;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pricesetdata = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set customerRef
     *
     * @param integer $customerRef
     * @return Pricesets
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add pricesetdata
     *
     * @param \Orth\IndexBundle\Entity\PricesetData $pricesetdata
     * @return Pricesets
     */
    public function addPricesetdatum(\Orth\IndexBundle\Entity\PricesetData $pricesetdata)
    {
        $this->pricesetdata[] = $pricesetdata;

        return $this;
    }

    /**
     * Remove pricesetdata
     *
     * @param \Orth\IndexBundle\Entity\PricesetData $pricesetdata
     */
    public function removePricesetdatum(\Orth\IndexBundle\Entity\PricesetData $pricesetdata)
    {
        $this->pricesetdata->removeElement($pricesetdata);
    }

    /**
     * Get pricesetdata
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPricesetdata()
    {
        return $this->pricesetdata;
    }
}
