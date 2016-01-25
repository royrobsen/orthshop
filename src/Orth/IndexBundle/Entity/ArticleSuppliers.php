<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleSuppliers
 */
class ArticleSuppliers
{
    /**
     * @var integer
     */
    private $articleRef;

    /**
     * @var integer
     */
    private $addressRef;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var float
     */
    private $price;

    /**
     * @var integer
     */
    private $priceUnit;

    /**
     * @var integer
     */
    private $amountUnit;

    /**
     * @var integer
     */
    private $vpe;

    /**
     * @var integer
     */
    private $minOrder;

    /**
     * @var integer
     */
    private $vpePackage;

    /**
     * @var integer
     */
    private $vpePalette;

    /**
     * @var string
     */
    private $supplierArticleNumber;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set articleRef
     *
     * @param integer $articleRef
     * @return ArticleSuppliers
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

    /**
     * Set addressRef
     *
     * @param integer $addressRef
     * @return ArticleSuppliers
     */
    public function setAddressRef($addressRef)
    {
        $this->addressRef = $addressRef;

        return $this;
    }

    /**
     * Get addressRef
     *
     * @return integer 
     */
    public function getAddressRef()
    {
        return $this->addressRef;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return ArticleSuppliers
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return ArticleSuppliers
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
     * Set priceUnit
     *
     * @param integer $priceUnit
     * @return ArticleSuppliers
     */
    public function setPriceUnit($priceUnit)
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    /**
     * Get priceUnit
     *
     * @return integer 
     */
    public function getPriceUnit()
    {
        return $this->priceUnit;
    }

    /**
     * Set amountUnit
     *
     * @param integer $amountUnit
     * @return ArticleSuppliers
     */
    public function setAmountUnit($amountUnit)
    {
        $this->amountUnit = $amountUnit;

        return $this;
    }

    /**
     * Get amountUnit
     *
     * @return integer 
     */
    public function getAmountUnit()
    {
        return $this->amountUnit;
    }

    /**
     * Set vpe
     *
     * @param integer $vpe
     * @return ArticleSuppliers
     */
    public function setVpe($vpe)
    {
        $this->vpe = $vpe;

        return $this;
    }

    /**
     * Get vpe
     *
     * @return integer 
     */
    public function getVpe()
    {
        return $this->vpe;
    }

    /**
     * Set minOrder
     *
     * @param integer $minOrder
     * @return ArticleSuppliers
     */
    public function setMinOrder($minOrder)
    {
        $this->minOrder = $minOrder;

        return $this;
    }

    /**
     * Get minOrder
     *
     * @return integer 
     */
    public function getMinOrder()
    {
        return $this->minOrder;
    }

    /**
     * Set vpePackage
     *
     * @param integer $vpePackage
     * @return ArticleSuppliers
     */
    public function setVpePackage($vpePackage)
    {
        $this->vpePackage = $vpePackage;

        return $this;
    }

    /**
     * Get vpePackage
     *
     * @return integer 
     */
    public function getVpePackage()
    {
        return $this->vpePackage;
    }

    /**
     * Set vpePalette
     *
     * @param integer $vpePalette
     * @return ArticleSuppliers
     */
    public function setVpePalette($vpePalette)
    {
        $this->vpePalette = $vpePalette;

        return $this;
    }

    /**
     * Get vpePalette
     *
     * @return integer 
     */
    public function getVpePalette()
    {
        return $this->vpePalette;
    }

    /**
     * Set supplierArticleNumber
     *
     * @param string $supplierArticleNumber
     * @return ArticleSuppliers
     */
    public function setSupplierArticleNumber($supplierArticleNumber)
    {
        $this->supplierArticleNumber = $supplierArticleNumber;

        return $this;
    }

    /**
     * Get supplierArticleNumber
     *
     * @return string 
     */
    public function getSupplierArticleNumber()
    {
        return $this->supplierArticleNumber;
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
     * @var \Orth\IndexBundle\Entity\Articles
     */
    private $articles;


    /**
     * Set articles
     *
     * @param \Orth\IndexBundle\Entity\Articles $articles
     * @return ArticleSuppliers
     */
    public function setArticles(\Orth\IndexBundle\Entity\Articles $articles = null)
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * Get articles
     *
     * @return \Orth\IndexBundle\Entity\Articles 
     */
    public function getArticles()
    {
        return $this->articles;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $variantvalues;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->variantvalues = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add variantvalues
     *
     * @param \Orth\IndexBundle\Entity\ArticleAttributeValues $variantvalues
     * @return ArticleSuppliers
     */
    public function addVariantvalue(\Orth\IndexBundle\Entity\ArticleAttributeValues $variantvalues)
    {
        $this->variantvalues[] = $variantvalues;

        return $this;
    }

    /**
     * Remove variantvalues
     *
     * @param \Orth\IndexBundle\Entity\ArticleAttributeValues $variantvalues
     */
    public function removeVariantvalue(\Orth\IndexBundle\Entity\ArticleAttributeValues $variantvalues)
    {
        $this->variantvalues->removeElement($variantvalues);
    }

    /**
     * Get variantvalues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVariantvalues()
    {
        return $this->variantvalues;
    }
    /**
     * @var string
     */
    private $attributes;


    /**
     * Set attributes
     *
     * @param string $attributes
     * @return ArticleSuppliers
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get attributes
     *
     * @return string 
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
