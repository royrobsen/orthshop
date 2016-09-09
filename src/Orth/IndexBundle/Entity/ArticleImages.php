<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleImages
 */
class ArticleImages
{
    /**
     * @var integer
     */
    private $articleRef;

    /**
     * @var string
     */
    private $picName;

    /**
     * @var string
     */
    private $mimetype;

    /**
     * @var integer
     */
    private $sorting;

    /**
     * @var integer
     */
    private $productRef;

    /**
     * @var integer
     */
    private $imgCrc32;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set articleRef
     *
     * @param integer $articleRef
     * @return ArticleImages
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
     * Set picName
     *
     * @param string $picName
     * @return ArticleImages
     */
    public function setPicName($picName)
    {
        $this->picName = $picName;

        return $this;
    }

    /**
     * Get picName
     *
     * @return string
     */
    public function getPicName()
    {
        return $this->picName;
    }

    /**
     * Set mimetype
     *
     * @param string $mimetype
     * @return ArticleImages
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * Get mimetype
     *
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * Set sorting
     *
     * @param integer $sorting
     * @return ArticleImages
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
     * Set productRef
     *
     * @param integer $productRef
     * @return ArticleImages
     */
    public function setProductRef($productRef)
    {
        $this->productRef = $productRef;

        return $this;
    }

    /**
     * Get productRef
     *
     * @return integer
     */
    public function getProductRef()
    {
        return $this->productRef;
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
     * @return ArticleImages
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
     * @var \Orth\IndexBundle\Entity\Articles
     */
    private $product;


    /**
     * Set product
     *
     * @param \Orth\IndexBundle\Entity\Articles $product
     * @return ArticleImages
     */
    public function setProduct(\Orth\IndexBundle\Entity\Articles $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Orth\IndexBundle\Entity\Articles
     */
    public function getProduct()
    {
        return $this->product;
    }

    private $toolTipColor;


    /**
     * Set toolTipColor
     *
     * @param integer $toolTipColor
     * @return ArticleImages
     */
    public function settoolTipColor($toolTipColor)
    {
        $this->toolTipColor = $toolTipColor;

        return $this;
    }

    /**
     * Get toolTipColor
     *
     * @return ArticleImages
     */
    public function getToolTipColor()
    {
        return $this->toolTipColor;
    }

    /**
     * Set imgCrc32
     *
     * @param integer $imgCrc32
     * @return ArticleImages
     */
    public function setImgCrc32($imgCrc32)
    {
        $this->imgCrc32 = $imgCrc32;

        return $this;
    }

    /**
     * Get imgCrc32
     *
     * @return integer
     */
    public function getImgCrc32()
    {
        return $this->imgCrc32;
    }

}
