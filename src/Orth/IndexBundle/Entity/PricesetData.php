<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PricesetData
 */
class PricesetData
{
    /**
     * @var integer
     */
    private $varRef;

    /**
     * @var float
     */
    private $price;

    /**
     * @var float
     */
    private $discount;

    /**
     * @var integer
     */
    private $pricesetRef;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Orth\IndexBundle\Entity\Pricesets
     */
    private $priceset;


    /**
     * Set varRef
     *
     * @param integer $varRef
     * @return PricesetData
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
     * Set price
     *
     * @param float $price
     * @return PricesetData
     */
    public function setPrice($price)
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
     * Set discount
     *
     * @param float $discount
     * @return PricesetData
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return float 
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set pricesetRef
     *
     * @param integer $pricesetRef
     * @return PricesetData
     */
    public function setPricesetRef($pricesetRef)
    {
        $this->pricesetRef = $pricesetRef;

        return $this;
    }

    /**
     * Get pricesetRef
     *
     * @return integer 
     */
    public function getPricesetRef()
    {
        return $this->pricesetRef;
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
     * Set priceset
     *
     * @param \Orth\IndexBundle\Entity\Pricesets $priceset
     * @return PricesetData
     */
    public function setPriceset(\Orth\IndexBundle\Entity\Pricesets $priceset = null)
    {
        $this->priceset = $priceset;

        return $this;
    }

    /**
     * Get priceset
     *
     * @return \Orth\IndexBundle\Entity\Pricesets 
     */
    public function getPriceset()
    {
        return $this->priceset;
    }
    /**
     * @var integer
     */
    private $articleRef;


    /**
     * Set articleRef
     *
     * @param integer $articleRef
     * @return PricesetData
     */
    public function setArticleRef($articleRef)
    {
        $this->articleRef = $articleRef;

        return $this;
    }

    /**
     * Get articleRef
     *
     * @return integer 
     */
    public function getArticleRef()
    {
        return $this->articleRef;
    }
}
