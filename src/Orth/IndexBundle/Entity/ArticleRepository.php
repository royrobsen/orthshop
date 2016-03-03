<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    public function getArticlesByCategory($catId)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM OrthIndexBundle:Articles a WHERE a.category = :id'
            )
            ->setParameter('id', $catId)
            ->getResult();

    }
    
    public function getArticleQuery($user, $searchTerm, $page,  $pageOffset, $category) 
    {
        
        $boolQuery = new \Elastica\Query\BoolQuery();
        
        if( $category != NULL AND $searchTerm == NULL ) {
            $categoryArray = [$category];
            $rootCategories = $this->getEntityManager()->getRepository('OrthIndexBundle:Categories')->findBy(array('parentId' => $category));
            
            foreach ($rootCategories as $childCategory ) {
                $childCategories = $this->getEntityManager()->getRepository('OrthIndexBundle:Categories')->findBy(array('parentId' => $childCategory->getId()));
                $categoryArray[] = $childCategory->getId();
                foreach ($childCategories as $grandchildCategory ) {
                    $categoryArray[] = $grandchildCategory->getId();
                }
            }
   
            $categoryQuery = new \Elastica\Query\Terms();
            $categoryQuery->setTerms('catRef', $categoryArray);
            $boolQuery->addMust($categoryQuery);
            
        } elseif ($category != NULL ) {
            
            $categoryQuery = new \Elastica\Query\Terms();
            $categoryQuery->setTerms('catRef', array($category));
            $boolQuery->addMust($categoryQuery);
        }
        
        if($searchTerm) {

            $fieldQuery = new \Elastica\Query\Match();
            $fieldQuery->setFieldQuery('allField', $searchTerm);
            $fieldQuery->setFieldOperator('allField', 'AND');
            $fieldQuery->setFieldMinimumShouldMatch('allField', '80%');
            $fieldQuery->setFieldAnalyzer('allField', 'custom_search_analyzer');
//
////            $fieldQuery = new \Elastica\Query\MultiMatch();
////            $fieldQuery->setFields(array('allField', 'shortName^30'));
////            $fieldQuery->setOperator('AND');
////            $fieldQuery->setMinimumShouldMatch('80%');
////            $fieldQuery->setQuery($searchTerm);
////            $fieldQuery->setAnalyzer('custom_search_analyzer');
            $boolQuery->addMust($fieldQuery);
            $fieldQuery = new \Elastica\Query\Match();
            $fieldQuery->setFieldQuery('shortName', '*' . $searchTerm . '*');
            $fieldQuery->setFieldOperator('shortName', 'AND');
            $fieldQuery->setFieldBoost('shortName', '5');
            //$fieldQuery->setFieldMinimumShouldMatch('shortName', '80%');
            ///$fieldQuery->setFieldAnalyzer('allField', 'custom_search_analyzer');
            $boolQuery->addShould($fieldQuery);
        } 
        
        $query = new \Elastica\Query();
        $query->setQuery($boolQuery);
        $query->setSize(12);
        $query->setFrom($pageOffset);        

        return $query;
    }
    
}