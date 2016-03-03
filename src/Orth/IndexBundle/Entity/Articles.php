<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Articles
 */
class Articles
{
    /**
     * @var string
     */
    
    private $showedPrice; 
    
    /**
     * @var string
     */
    
    private $priceDiff; 
    
    /**
     * @var string
     */
    private $shortName;
    
    /**
     * @var string
     */
    private $shortName1;

    /**
     * @var string
     */
    private $shortDescription;

    /**
     * @var string
     */
    private $longDescription;

    /**
     * @var \DateTime
     */
    private $modifiedDate;

    /**
     * @var integer
     */
    private $catRef;

    /**
     * @var integer
     */
    private $deliverable;

    /**
     * @var integer
     */
    private $id;

    /**
     * Set shortName
     *
     * @param string $shortName
     * @return Articles
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string 
     */
    public function getShortName()
    {
        return $this->shortName;
    }
    
    /**
     * Set shortName1
     *
     * @param string $shortName
     * @return Articles
     */
    public function setShortName1($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName1
     *
     * @return string 
     */
    public function getShortName1()
    {
        return $this->shortName;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return Articles
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string 
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set longDescription
     *
     * @param string $longDescription
     * @return Articles
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    /**
     * Get longDescription
     *
     * @return string 
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * Set modifiedDate
     *
     * @param \DateTime $modifiedDate
     * @return Articles
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * Get modifiedDate
     *
     * @return \DateTime 
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }

    /**
     * Set catRef
     *
     * @param integer $catRef
     * @return Articles
     */
    public function setCatRef($catRef)
    {
        $this->catRef = $catRef;

        return $this;
    }

    /**
     * Get catRef
     *
     * @return integer 
     */
    public function getCatRef()
    {
        return $this->catRef;
    }

    /**
     * Set deliverable
     *
     * @param integer $deliverable
     * @return Articles
     */
    public function setDeliverable($deliverable)
    {
        $this->deliverable = $deliverable;

        return $this;
    }

    /**
     * Get deliverable
     *
     * @return integer 
     */
    public function getDeliverable()
    {
        return $this->deliverable;
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
    private $images;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add images
     *
     * @param \Orth\IndexBundle\Entity\ArticleImages $images
     * @return Articles
     */
    public function addImage(\Orth\IndexBundle\Entity\ArticleImages $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Orth\IndexBundle\Entity\ArticleImages $images
     */
    public function removeImage(\Orth\IndexBundle\Entity\ArticleImages $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $variants;


    /**
     * Add variants
     *
     * @param \Orth\IndexBundle\Entity\ArticleSuppliers $variants
     * @return Articles
     */
    public function addVariant(\Orth\IndexBundle\Entity\ArticleSuppliers $variants)
    {
        $this->variants[] = $variants;

        return $this;
    }

    /**
     * Remove variants
     *
     * @param \Orth\IndexBundle\Entity\ArticleSuppliers $variants
     */
    public function removeVariant(\Orth\IndexBundle\Entity\ArticleSuppliers $variants)
    {
        $this->variants->removeElement($variants);
    }

    /**
     * Get variants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVariants()
    {
        return $this->variants;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $custdata;


    /**
     * Add custdata
     *
     * @param \Orth\IndexBundle\Entity\Customerdata $custdata
     * @return Articles
     */
    public function addCustdatum(\Orth\IndexBundle\Entity\Customerdata $custdata)
    {
        $this->custdata[] = $custdata;

        return $this;
    }

    /**
     * Remove custdata
     *
     * @param \Orth\IndexBundle\Entity\Customerdata $custdata
     */
    public function removeCustdatum(\Orth\IndexBundle\Entity\Customerdata $custdata)
    {
        $this->custdata->removeElement($custdata);
    }

    /**
     * Get custdata
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCustdata()
    {
        return $this->custdata;
    }
    
    private $allField;
    
    /**
     * Set allFields
     *
     * @param string allField
     * @return Articles
     */
    public function setAllField($allField)
    {
        $this->allField = $allField;

        return $this;
    }

    /**
     * Get allField
     *
     * @return string 
     */
    public function getAllField()
    {
        return $this->allField;
    }
    
    private $allcustField;
    
    /**
     * Set allcustFields
     *
     * @param string allcustField
     * @return Articles
     */
    public function setAllcustField($allcustField)
    {
        $this->allcustField = $allcustField;

        return $this;
    }

    /**
     * Get allcustField
     *
     * @return string 
     */
    public function getAllcustField()
    {
        return $this->allcustField;
    }
    
    /**
     * Set showedPrice
     *
     * @param string $showedPrice
     * @return Articles
     */
    public function setShowedPrice($showedPrice)
    {
        $this->showedPrice = $showedPrice;

        return $this;
    }

    /**
     * Get showedPrice
     *
     * @return string 
     */
    public function getShowedPrice()
    {
        return $this->showedPrice;
    }
    
    private $attachment;
    
    /**
     * Set attachment
     */
    public function setAttachment($attachment = null) 
    {
        $this->attachment = $attachment;
        
        return $this;
    }
    
    /**
     * Get attachment
     */
            
    public function getAttachment()
    {
        return $this->attachment;
    }
    
    /**
     * @var \Orth\IndexBundle\Entity\Categories
     */
    private $category;


    /**
     * Set category
     *
     * @param \Orth\IndexBundle\Entity\Categories $category
     * @return Articles
     */
    public function setCategory(\Orth\IndexBundle\Entity\Categories $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Orth\IndexBundle\Entity\Categories 
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * Set priceDiff
     *
     * @param string $priceDiff
     * @return Articles
     */
    public function setPriceDiff($priceDiff)
    {
        $this->priceDiff = $priceDiff;

        return $this;
    }

    /**
     * Get priceDiff
     *
     * @return string 
     */
    public function getPriceDiff()
    {
        return $this->priceDiff;
    }
}
