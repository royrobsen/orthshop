<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleAttributes
 */
class ArticleAttributes
{
    /**
     * @var string
     */
    private $attributeName;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set attributeName
     *
     * @param string $attributeName
     * @return ArticleAttributes
     */
    public function setAttributeName($attributeName)
    {
        $this->attributeName = $attributeName;

        return $this;
    }

    /**
     * Get attributeName
     *
     * @return string 
     */
    public function getAttributeName()
    {
        return $this->attributeName;
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $attrValue;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attrValue = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add attrValue
     *
     * @param \Orth\IndexBundle\Entity\ArticleAttributeValues $attrValue
     * @return ArticleAttributes
     */
    public function addAttrValue(\Orth\IndexBundle\Entity\ArticleAttributeValues $attrValue)
    {
        $this->attrValue[] = $attrValue;

        return $this;
    }

    /**
     * Remove attrValue
     *
     * @param \Orth\IndexBundle\Entity\ArticleAttributeValues $attrValue
     */
    public function removeAttrValue(\Orth\IndexBundle\Entity\ArticleAttributeValues $attrValue)
    {
        $this->attrValue->removeElement($attrValue);
    }

    /**
     * Get attrValue
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttrValue()
    {
        return $this->attrValue;
    }
    
    
}
