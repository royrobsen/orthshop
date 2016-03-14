<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    public function checkForChildrenCategories($id)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM OrthIndexBundle:Categories c WHERE c.parentId = :id'
            )->setParameter('id', $id)
            ->getResult();
        if ( $query != NULL ) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
    
    public function checkForSubArticles($id)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM OrthIndexBundle:Categories c JOIN c.article a WHERE c.id = :id'
            )->setParameter('id', $id)
            ->getResult();
        if ( $query != NULL ) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
    
    public function getAllCategories()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM OrthIndexBundle:Categories c'
            )
            ->getResult();
    }
    
    public function getAllChildCategories($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM OrthIndexBundle:Categories c WHERE c.parentId = :id'
            )
            ->setParameter('id', $id)
            ->getResult();

    }
    
    public function getCategoryPath ($category, $value = 0) 
    {
        
        if ($category == NULL) {
            return 'Suche';
        }
        
        $query = $this->getEntityManager()
            ->createQuery('SELECT c, c1, c2 FROM OrthIndexBundle:Categories c LEFT JOIN c.parent c1 LEFT JOIN c1.parent c2 WHERE c.id = :id')->setParameter('id', $category)->getResult();
   
        $test[] = array('id' => $query[0]->getId(), 'parentId' => $query[0]->getParentId(),'catName' => $query[0]->getCategoryName(), 'anzahl' => $value);
            if($query[0]->getParent() != NULL) {
                $test[] = array('id' => $query[0]->getParent()->getId(), 'parentId' => $query[0]->getParent()->getParentId(),'catName' => $query[0]->getParent()->getCategoryName(), 'anzahl' => 0);
            
            if($query[0]->getParent()->getParent() != NULL) {
                $test[] = array('id' => $query[0]->getParent()->getParent()->getId(), 'parentId' => $query[0]->getParent()->getParent()->getParentId(),'catName' => $query[0]->getParent()->getParent()->getCategoryName(), 'anzahl' => 0);
            }}
           
            return $this->buildTree( array_unique($test, SORT_REGULAR) );
        
    }
    
    public function buildTree( $ar, $pid = null ) {
        $op = array();
        foreach( $ar as $item ) {
            if( $item['parentId'] == $pid ) {
                $op[$item['id']] = array(
                    'id' => $item['id'],
                    'catName' => $item['catName'],
                    'parentId' => $item['parentId'],
                    'anzahl' => $item['anzahl']
                );
                // using recursion
                $children =  $this->buildTree( $ar, $item['id'] );
                if( $children ) {
                    $op[$item['id']]['children'] = $children;
                }
            }
        }
        return $op;
    }
    
    public function getRootCategory($category) 
    {
        
        if ($category == NULL) {
            return 'Suche';
        }
        
        $query = $this->getEntityManager()
            ->createQuery('SELECT c, c1, c2 FROM OrthIndexBundle:Categories c LEFT JOIN c.parent c1 LEFT JOIN c1.parent c2 WHERE c.id = :id')->setParameter('id', $category)->getResult();
        
        return  $query[0]->getId();

        
    }
    
}