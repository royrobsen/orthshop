<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

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

    public function getArticleQuery2($user, $searchTerm, $page,  $pageOffset, $category, $orderby)
    {

        $boolQuery = new \Elastica\Query\BoolQuery();

        if( $category != NULL) {
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

        }

        if( $searchTerm ) {

            $fieldQuery = new \Elastica\Query\Match();

            $fieldQuery->setFieldQuery('allField', $searchTerm);
            $fieldQuery->setFieldOperator('allField', 'AND');
            $fieldQuery->setFieldMinimumShouldMatch('allField', '70%');
            $fieldQuery->setFieldFuzziness('allField', '0.8');

            $fieldQuery->setFieldAnalyzer('allField', 'custom_search_analyzer');

            $boolQuery->addMust($fieldQuery);

        }

        $agg = new \Elastica\Aggregation\Terms("catRef");
        $agg->setSize(5000000);
        $agg->setField('catRef');

        $boolFilter = new \Elastica\Filter\Bool();

        if($user == "anon.") {
            $boolFilter->addShould(
                    new \Elastica\Filter\Terms('customized', array(0))
                );
        } else {
            $boolFilter->addShould(
                    new \Elastica\Filter\Terms('customized', array(0,$user->getCustomerRef()))
                );
        }

        $filtered = new \Elastica\Query\Filtered($boolQuery, $boolFilter);
        $query = new \Elastica\Query();
        $query->setQuery($filtered);
        $query->addAggregation($agg);


        if($orderby == 'desc') {
            $query->setSort(array('variants.price' => array('order' => 'desc')));
        } elseif($orderby == 'asc') {
            $query->setSort(array('variants.price' => array('order' => 'asc')));
        }

        return $query;
    }

    public function getArticleQuery3($searchTerm, $articleRef)
    {

        $boolQuery = new \Elastica\Query\BoolQuery();

        if( $searchTerm ) {

            $fieldQuery = new \Elastica\Query\Match();
            $fieldQuery->setFieldQuery('desc1', $searchTerm);
            $fieldQuery->setFieldOperator('desc1', 'OR');
            $fieldQuery->setFieldMinimumShouldMatch('desc1', '80%');
            $fieldQuery->setFieldAnalyzer('desc1', 'custom_search_analyzer');

            $boolQuery->addShould($fieldQuery);

            $fieldQuery = new \Elastica\Query\Match();
            $fieldQuery->setFieldQuery('desc2', $searchTerm);
            $fieldQuery->setFieldOperator('desc2', 'OR');
            $fieldQuery->setFieldMinimumShouldMatch('desc2', '80%');
            $fieldQuery->setFieldAnalyzer('desc2', 'custom_search_analyzer');

            $boolQuery->addShould($fieldQuery);

        }
        $agg = new \Elastica\Aggregation\Terms("attributeValues");
        $agg->setSize(5000000);
        $agg->setField('attributeValues');
        $articleRefQuery = new \Elastica\Query\Terms();

        $articleRefQuery->setTerms('articleRef', array($articleRef));
        $boolQuery->addMust($articleRefQuery);

        $query = new \Elastica\Query();
        $query->setQuery($boolQuery);
        $query->addAggregation($agg);

        $agg2 = new \Elastica\Aggregation\Terms("variantsattr");
        $agg2->setField('variantvalues.attributeValue');
        $agg2->setSize(500);
        $filter = new \Elastica\Filter\Term();
        $filter->setTerm('attributeRef', 1);

        $agg4 = new \Elastica\Aggregation\Filters('filterColor');
        $agg4->addFilter($filter);
        $agg4->addAggregation($agg2);

        $cAgg = new \Elastica\Aggregation\Nested('colors', 'variantvalues');
        $cAgg->addAggregation($agg4);

        //$cAgg->setParam('filter', $agg3);

        $query->addAggregation($cAgg);
        return $query;

    }

    public function getColorAttributes($allArticles, $elasticIndexVariants, $colors) {

      foreach ( $allArticles as $article ) {

        $article_ids[] = $article->getId();

      }

      $boolQuery = new \Elastica\Query\BoolQuery();

      $articleRefQuery = new \Elastica\Query\Terms();
      $articleRefQuery->setTerms('articleRef', $article_ids);

      $boolQuery->addMust($articleRefQuery);

      $query = new \Elastica\Query();
      $query->setQuery($boolQuery);
      $agg2 = new \Elastica\Aggregation\Terms("variantsattr");
      $agg2->setField('variantvalues.otherTerms');
      $agg2->setSize(50);
      $filter = new \Elastica\Filter\Term();
      $filter->setTerm('attributeRef', 1);

      $agg4 = new \Elastica\Aggregation\Filters('filterColor');
      $agg4->addFilter($filter);
      $agg4->addAggregation($agg2);

      $cAgg = new \Elastica\Aggregation\Nested('colors', 'variantvalues');
      $cAgg->addAggregation($agg4);

      $query->addAggregation($cAgg);

      $aggResult = $elasticIndexVariants->search($query);

      $colors = $aggResult->getAggregations();
      $result = [];
      foreach ($colors['colors']['filterColor']['buckets'][0]['variantsattr']['buckets'] as $color) {
        $result[] = $color;
      }

      return $result;

  }

      public function getArticles($user, $searchTerm, $page, $pageOffset, $category, $orderby, $colors, $finder, $elasticIndex) {

          $boolQuery = new \Elastica\Query\BoolQuery();

          if( $category != NULL) {

              $query = $this->getEntityManager()->createQuery( "SELECT c.id FROM OrthIndexBundle:Categories c WHERE c.id LIKE :category")
                      ->setParameter('category', $category . "%");

              $queryResult = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

              foreach ($queryResult as $categoryId) {
                $categoryArray[] = $categoryId['id'];
              }

              $categoryQuery = new \Elastica\Query\Terms();
              $categoryQuery->setTerms('catRef', $categoryArray);
              $boolQuery->addMust($categoryQuery);

          }

          if( $searchTerm ) {

              $fieldQuery = new \Elastica\Query\Match();

              $fieldQuery->setFieldQuery('allField', $searchTerm);
              $fieldQuery->setFieldOperator('allField', 'AND');
              $fieldQuery->setFieldMinimumShouldMatch('allField', '70%');
              $fieldQuery->setFieldFuzziness('allField', '0.8');

              $fieldQuery->setFieldAnalyzer('allField', 'custom_search_analyzer');

              $boolQuery->addMust($fieldQuery);

          }

          if ( $colors != NULL ) {

            $colorQuery = new \Elastica\Query\Terms();
            $colorQuery->setTerms('variants.variantvalues.otherTerms', $colors);
            $colorNested = new \Elastica\Query\Nested('variants');
            $colorNested->setPath('variants.variantvalues');
            $colorNested->setQuery($colorQuery);

            $boolQuery->addMust($colorNested);

          }

          $agg = new \Elastica\Aggregation\Terms("catRef");
          $agg->setSize(5000);
          $agg->setField('catRef');

          $boolFilter = new \Elastica\Filter\Bool();

          if($user == "anon.") {
              $boolFilter->addShould(
                      new \Elastica\Filter\Terms('customized', array(0))
                  );
          } else {
              $boolFilter->addShould(
                      new \Elastica\Filter\Terms('customized', array(0,$user->getCustomerRef()))
                  );
          }

          $filtered = new \Elastica\Query\Filtered($boolQuery, $boolFilter);

          $query = new \Elastica\Query();
          $query->setQuery($filtered);
          //if( $colors != NULL) {
          //  $query->setFilter($colorNested);
          //}

          if($orderby == 'desc') {
              $query->setSort(array('variants.price' => array('order' => 'desc')));
          } elseif($orderby == 'asc') {
              $query->setSort(array('variants.price' => array('order' => 'asc')));
          }

          $query->addAggregation($agg);
          $query->setSize(12);
          $query->setFrom($pageOffset);
          $articles = $finder->find($query);
          $aggregations = $elasticIndex->search($query);

          $result = array("articles" => $articles, "aggs" => $aggregations, "rQuery" => $query);

        return $result;

    }

    public function getBestImg($searchTerm, $articleRef, $colors, $finder) {

      $boolQuery = new \Elastica\Query\BoolQuery();

      if( $searchTerm ) {

          $fieldQuery = new \Elastica\Query\Match();
          $fieldQuery->setFieldQuery('desc1', $searchTerm);
          $fieldQuery->setFieldOperator('desc1', 'OR');
          $fieldQuery->setFieldMinimumShouldMatch('desc1', '80%');
          $fieldQuery->setFieldAnalyzer('desc1', 'custom_search_analyzer');

          $boolQuery->addShould($fieldQuery);

          $fieldQuery = new \Elastica\Query\Match();
          $fieldQuery->setFieldQuery('desc2', $searchTerm);
          $fieldQuery->setFieldOperator('desc2', 'OR');
          $fieldQuery->setFieldMinimumShouldMatch('desc2', '80%');
          $fieldQuery->setFieldAnalyzer('desc2', 'custom_search_analyzer');

          $varTerms = explode(" ", $searchTerm);

          $boolQuery->addShould($fieldQuery);
          $colorQuery = new \Elastica\Query\Terms();
          $colorQuery->setTerms('variantvalues.otherTerms', $varTerms);
          $colorNested = new \Elastica\Query\Nested('variantvalues');
          $colorNested->setPath('variantvalues');
          $colorNested->setQuery($colorQuery);

          $boolQuery->addShould($colorNested);

          if ( $colors != NULL ) {
            $fieldQuery = new \Elastica\Query\Terms();
            $fieldQuery->setTerms('otherTerms', $colors);

            $boolQuery->addShould($fieldQuery);
          }

      }

      $articleRefQuery = new \Elastica\Query\Terms();
      $articleRefQuery->setTerms('articleRef', array($articleRef));

      $boolQuery->addMust($articleRefQuery);

      if ( $colors != NULL ) {

        $colorQuery = new \Elastica\Query\Terms();
        $colorQuery->setTerms('variantvalues.otherTerms', $colors);
        $colorNested = new \Elastica\Query\Nested('variantvalues');
        $colorNested->setPath('variantvalues');
        $colorNested->setQuery($colorQuery);

        $boolQuery->addMust($colorNested);

      }

      $query = new \Elastica\Query();
      $query->setQuery($boolQuery);
      $query->setSize(12);
      $articleVar = $finder->find($query);

      return $articleVar;
    }

    //
    // function to build a category tree view of given grandchildcategories
    //
    public function buildCatTree($categories) {

      // mapping for native SQL-Query
      $rsm = new ResultSetMapping();
      $rsm->addEntityResult('Orth\IndexBundle\Entity\Categories', 'c');
      $rsm->addFieldResult('c', 'id', 'id'); // ($alias, $columnName, $fieldName)
      $rsm->addFieldResult('c', 'category_name', 'categoryName'); // // ($alias, $columnName, $fieldName)
      $rsm->addFieldResult('c', 'parent_id', 'parentId'); // // ($alias, $columnName, $fieldName)

      // create new array for plain key and key + doc_count
      foreach ($categories as $cat) {
          $categoryList['keys'][] = $cat['key'];
          $categoryList['keys'][] = substr($cat['key'], 0, 2);
          $categoryList['keys'][] = substr($cat['key'], 0, 4);
          $categoryList['count'][$cat['key']] = $cat['doc_count'];
      }

      // native SQL-Query
      $dbCategory = $this->getEntityManager()->createNativeQuery('SELECT id, category_name, parent_id FROM categories WHERE id IN (?)', $rsm);
      // set unique array as parameter for native SQL-Squery
      $dbCategory->setParameter(1, array_unique($categoryList['keys']));

      // returns simple array, instead of entity resultset
      $categoryData = $dbCategory->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

      // re-create a final array to add key 'anzahl'
      foreach ($categoryData as &$catData) {
        if(strlen($catData['id']) == 6) {
          $catData['anzahl'] = $categoryList['count'][$catData['id']];
        } else {
          $catData['anzahl'] = 0;
        }
      }

      // returns parent-child-array for category tree view
      $result = $this->buildTree( array_unique($categoryData, SORT_REGULAR) );

      return $result;

    }

    public function buildTree( $ar, $pid = null ) {
        $op = array();
        foreach( $ar as $item ) {
            if( $item['parentId'] == $pid ) {
                $op[$item['id']] = array(
                    'id' => $item['id'],
                    'catName' => $item['categoryName'],
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
}
