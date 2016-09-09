<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleAttributeValues
 */
class ArticleAttributeValues
{
    /**
     * @var integer
     */
    private $attributeRef;

    /**
     * @var string
     */
    private $attributeValue;

    /**
     * @var string
     */
    private $attributeUnit;

    /**
     * @var string
     */
    private $otherTerms;
    
    /**
     * @var integer
     */
    private $sorting;

    /**
     * @var integer
     */
    private $varRef;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set attributeRef
     *
     * @param integer $attributeRef
     * @return ArticleAttributeValues
     */
    public function setAttributeRef($attributeRef)
    {
        $this->attributeRef = $attributeRef;

        return $this;
    }

    /**
     * Get attributeRef
     *
     * @return integer 
     */
    public function getAttributeRef()
    {
        return $this->attributeRef;
    }

    /**
     * Set attributeValue
     *
     * @param string $attributeValue
     * @return ArticleAttributeValues
     */
    public function setAttributeValue($attributeValue)
    {
        $this->attributeValue = $attributeValue;

        return $this;
    }

    /**
     * Get attributeValue
     *
     * @return string 
     */
    public function getAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * Set attributeUnit
     *
     * @param string $attributeUnit
     * @return ArticleAttributeValues
     */
    public function setAttributeUnit($attributeUnit)
    {
        $this->attributeUnit = $attributeUnit;

        return $this;
    }

    /**
     * Get attributeUnit
     *
     * @return string 
     */
    public function getAttributeUnit()
    {
        return $this->attributeUnit;
    }

    /**
     * Set otherTerms
     *
     * @param string $otherTerms
     * @return ArticleAttributeValues
     */
    public function setOtherTerms($otherTerms)
    {
        $this->otherTerms = $otherTerms;

        return $this;
    }

    /**
     * Get otherTerms
     *
     * @return string 
     */
    public function getOtherTerms()
    {
        return $this->otherTerms;
    }

    
    /**
     * Set sorting
     *
     * @param integer $sorting
     * @return ArticleAttributeValues
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * Get sorting
     *
     * @return integer 
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Set varRef
     *
     * @param integer $varRef
     * @return ArticleAttributeValues
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var \Orth\IndexBundle\Entity\ArticleSuppliers
     */
    private $variants;


    /**
     * Set variants
     *
     * @param \Orth\IndexBundle\Entity\ArticleSuppliers $variants
     * @return ArticleAttributeValues
     */
    public function setVariants(\Orth\IndexBundle\Entity\ArticleSuppliers $variants = null)
    {
        $this->variants = $variants;

        return $this;
    }

    /**
     * Get variants
     *
     * @return \Orth\IndexBundle\Entity\ArticleSuppliers 
     */
    public function getVariants()
    {
        return $this->variants;
    }
    /**
     * @var \Orth\IndexBundle\Entity\ArticleAttributes
     */
    private $attrName;


    /**
     * Set attrName
     *
     * @param \Orth\IndexBundle\Entity\ArticleAttributes $attrName
     * @return ArticleAttributeValues
     */
    public function setAttrName(\Orth\IndexBundle\Entity\ArticleAttributes $attrName = null)
    {
        $this->attrName = $attrName;

        return $this;
    }

    /**
     * Get attrName
     *
     * @return \Orth\IndexBundle\Entity\ArticleAttributes 
     */
    public function getAttrName()
    {
        return $this->attrName;
    }
}
