<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categories
 */
class Categories
{
    /**
     * @var string
     */
    private $categoryName;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set categoryName
     *
     * @param string $categoryName
     * @return Categories
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    /**
     * Get categoryName
     *
     * @return string 
     */
    public function getCategoryName()
    {
        return $this->categoryName;
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
    private $parentId;


    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return Categories
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parentId;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Orth\IndexBundle\Entity\Categories
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add children
     *
     * @param \Orth\IndexBundle\Entity\Categories $children
     * @return Categories
     */
    public function addChild(\Orth\IndexBundle\Entity\Categories $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Orth\IndexBundle\Entity\Categories $children
     */
    public function removeChild(\Orth\IndexBundle\Entity\Categories $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Orth\IndexBundle\Entity\Categories $parent
     * @return Categories
     */
    public function setParent(\Orth\IndexBundle\Entity\Categories $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Orth\IndexBundle\Entity\Categories 
     */
    public function getParent()
    {
        return $this->parent;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $article;


    /**
     * Add article
     *
     * @param \Orth\IndexBundle\Entity\Articles $article
     * @return Categories
     */
    public function addArticle(\Orth\IndexBundle\Entity\Articles $article)
    {
        $this->article[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \Orth\IndexBundle\Entity\Articles $article
     */
    public function removeArticle(\Orth\IndexBundle\Entity\Articles $article)
    {
        $this->article->removeElement($article);
    }

    /**
     * Get article
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticle()
    {
        return $this->article;
    }
    
    public function getParentName() { 
        if ($this->getParent() AND $this->getParent()->getParent()) {
            $parentName = $this->getParent()->getParent()->getCategoryName() . " -> " . $this->getParent()->getCategoryName();
        } 
        elseif ($this->getParent() AND !$this->getParent()->getParent()) { 
            $parentName = $this->getParent()->getCategoryName();
        }
        else {
            $parentName = null;
        }
        //$parentName = $this->getParent() ? $this->getParent()->getCategoryName() : null;      
        
        return $parentName;
    }
   
    
    public function getCatForArticleName() {
        if ($this->getParent()) {
            $parentName = $this->getParent()->getCategoryName();
        } else {
            $parentName = null;
        }
        //$parentName = $this->getParent() ? $this->getParent()->getCategoryName() : null;      
        
        return $parentName;
    }
}
