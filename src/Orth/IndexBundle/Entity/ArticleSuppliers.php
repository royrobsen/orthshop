<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;


/**
 * ArticleSuppliers
 */
class ArticleSuppliers extends EntityRepository
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
     * @var string
     */
    private $desc1;
    
    /**
     * @var string
     */
    private $desc2;   

    /**
     * @var string
     */
    private $detailtext;
    
    /**
     * @var string
     */
    private $gtin;
    
    /**
     * @var integer
     */
    private $customized;
    
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
    
    /**
     * Set desc1
     *
     * @param string $desc1
     * @return Desc1
     */
    public function setDesc1($desc1)
    {
        $this->desc1 = $desc1;

        return $this;
    }

    /**
     * Get desc1
     *
     * @return string 
     */
    public function getDesc1()
    {
        return $this->desc1;
    }
    
    /**
     * Set desc2
     *
     * @param string $desc2
     * @return Desc2
     */
    public function setDesc2($desc2)
    {
        $this->desc2 = $desc2;

        return $this;
    }

    /**
     * Get desc2
     *
     * @return string 
     */
    public function getDesc2()
    {
        return $this->desc2;
    }
    
    /**
     * Set detailtext
     *
     * @param string $detailtext
     * @return Detailtext
     */
    public function setDetailtext($detailtext)
    {
        $this->detailtext= $detailtext;

        return $this;
    }

    /**
     * Get detailtext
     *
     * @return string 
     */
    public function getDetailtext()
    {
        return $this->detailtext;
    }
    
    /**
     * Set gtin
     *
     * @param string $gtin
     * @return Gtin
     */
    public function setGtin($gtin)
    {
        $this->gtin = $gtin;

        return $this;
    }

    /**
     * Get gtin
     *
     * @return string 
     */
    public function getGtin()
    {
        return $this->gtin;
    }
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $images;


    /**
     * Add images
     *
     * @param \Orth\IndexBundle\Entity\ArticleImages $images
     * @return ArticleSuppliers
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
     * Set id
     *
     * @param integer $id
     * @return ArticleSuppliers
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    
    private $allFieldVar;
    
    /**
     * Set allFieldVar
     *
     * @param string allFieldVar
     * @return Articles
     */
    public function setAllFieldVar($allFieldVar)
    {
        $this->allFieldVar = $allFieldVar;

        return $this;
    }

    /**
     * Get allFieldVar
     *
     * @return string 
     */
    public function getAllFieldVar()
    {
        return $this->allFieldVar;
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
     * Set customized
     *
     * @param integer $customized
     * @return ArticleSuppliers
     */
    public function setCustomized($customized)
    {
        $this->customized = $customized;

        return $this;
    }

    /**
     * Get customized
     *
     * @return integer 
     */
    public function getCustomized()
    {
        return $this->customized;
    }

     public function getColorByAttr($varId) {
        
        $colorQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT aav FROM OrthIndexBundle:ArticleAttributeValues aav WHERE aav.varRef = :varId AND aav.attributeRef = 1'
            )->setParameter('varId', $varId)->setMaxResults(1)->getOneOrNullResult();
        
        return $colorQuery->getAttributeValue();
    }
    
    private $color;
    
    /**
     * Set color
     *
     * @param string color
     * @return Articles
     */
    public function setColor($id)
    {
        $this->color = getColorByAttr($id);

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    
    public function getColor()
    {
        return $this->color;
    }
   
    private $color2;
    
    /**
     * Set color2
     *
     * @param string color2
     * @return Articles
     */
    public function setColor2($id)
    {
        $this->color = $color2;

        return $this;
    }

    /**
     * Get color2
     *
     * @return string 
     */
    
    public function getColor2()
    {
        return $this->color2;
    }    
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $custdata;


    /**
     * Add custdata
     *
     * @param \Orth\IndexBundle\Entity\Customerdata $custdata
     * @return ArticleSuppliers
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
}
