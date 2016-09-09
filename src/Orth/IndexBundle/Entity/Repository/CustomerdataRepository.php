<?php

namespace Orth\IndexBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class CustomerdataRepository extends EntityRepository
{

  public function getArticles($user, $searchTerm, $page, $pageOffset, $category, $colors, $finder) {

      $boolQuery = new \Elastica\Query\BoolQuery();

      if ( $category != NULL) {

      $categoryQuery = new \Elastica\Query\Terms();
      $categoryQuery->setTerms('custdata.customCatRef', array($category));

      $boolQuery->addMust($categoryQuery);
      }

      if( $searchTerm ) {

          $fieldQuery = new \Elastica\Query\Match();
          $fieldQuery->setFieldQuery('customerRef',$user->getCustomerRef());
          $boolQuery->addMust($fieldQuery);

          $fieldQuery = new \Elastica\Query\Match();

          $fieldQuery->setFieldQuery('allField', $searchTerm);
          $fieldQuery->setFieldOperator('allField', 'AND');
          $fieldQuery->setFieldMinimumShouldMatch('allField', '70%');
          $fieldQuery->setFieldFuzziness('allField', '0.8');

          $fieldQuery->setFieldAnalyzer('allField', 'custom_search_analyzer');

          $boolQuery->addMust($fieldQuery);

      } else {
          $fieldQuery = new \Elastica\Query\MatchAll();
          $boolQuery->addMust($fieldQuery);
      }

      $fieldQuery = new \Elastica\Query\Nested();
      $fieldQuery->setPath('custdata.custcat.perm');
      $boolNested = new \Elastica\Query\BoolQuery();
      $fieldNested = new \Elastica\Query\Match();
      $fieldNested->setField('custdata.custcat.perm.permStatus', 1);
      $boolNested->addMust($fieldNested);

      $fieldNested = new \Elastica\Query\Match();
      $fieldNested->setField('custdata.custcat.perm.userRef', $user->getId());
      $boolNested->addMust($fieldNested);
      $fieldQuery->setQuery($boolNested);
      $boolQuery->addMust($fieldQuery);

      if ( $colors != NULL ) {

          $colorQuery = new \Elastica\Query\Terms();
          $colorQuery->setTerms('variants.variantvalues.otherTerms', $colors);
          $colorNested = new \Elastica\Query\Nested('variants');
          $colorNested->setPath('variants.variantvalues');
          $colorNested->setQuery($colorQuery);

          $boolQuery->addMust($colorNested);

      }

      $query = new \Elastica\Query();
      $query->setQuery($boolQuery);
      $query->setSize(12);
      $query->setFrom($pageOffset);
      $articles = $finder->find($query);

      $result = array("articles" => $articles, "rQuery" => $query);

    return $result;

  }

}
