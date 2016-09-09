<?php

namespace Orth\IndexBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CustomercategoryRepository extends EntityRepository
{

    // returns array of custom categories with subarrays of children categories by customer
    public function getCategoryCustom($user)
    {

        $qb = $this->createQueryBuilder('cc')
             ->select('cc')
             ->where('cc.customerRef =:customerRef')->setParameter('customerRef', $user->getCustomerRef())
             ->getQuery()
             ->getResult(Query::HYDRATE_ARRAY);

        return $this->buildTree($qb, SORT_REGULAR);
    }

    // returns 'one or null result' of custom categories by customer and id
    public function getCategoryCustomById($id, $user)
    {

        $qb = $this->createQueryBuilder('cc')
             ->select('cc')
             ->where('cc.customerRef = :customerRef AND cc.id = :id')->setParameter('customerRef', $user->getCustomerRef())->setParameter( 'id', $id)
             ->getQuery()
             ->getOneOrNullResult();

        return $qb;
    }

    private function buildTree( $ar, $pid = null ) {
            $op = array();
            foreach( $ar as $item ) {
                if( $item['parentId'] == $pid ) {
                    $op[$item['id']] = array(
                        'id' => $item['id'],
                        'catName' => $item['catName'],
                        'parentId' => $item['parentId']
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

        public function getCustomCategoryPath ($category, $value = 0)
        {

            if ($category == NULL) {
                return 'Suche';
            }

            $query = $this->getEntityManager()
                ->createQuery('SELECT cc, cc1, cc2 FROM OrthIndexBundle:Customcategory cc LEFT JOIN cc.parent cc1 LEFT JOIN cc1.parent cc2 WHERE cc.id = :id')->setParameter('id', $category)->getResult();

            $test[] = array('id' => $query[0]->getId(), 'parentId' => $query[0]->getParentId(),'catName' => $query[0]->getCategoryName(), 'anzahl' => $value);
                if($query[0]->getParent() != NULL) {
                    $test[] = array('id' => $query[0]->getParent()->getId(), 'parentId' => $query[0]->getParent()->getParentId(),'catName' => $query[0]->getParent()->getCategoryName(), 'anzahl' => 0);

                if($query[0]->getParent()->getParent() != NULL) {
                    $test[] = array('id' => $query[0]->getParent()->getParent()->getId(), 'parentId' => $query[0]->getParent()->getParent()->getParentId(),'catName' => $query[0]->getParent()->getParent()->getCategoryName(), 'anzahl' => 0);
                }}

                return $this->buildTree( array_unique($test, SORT_REGULAR) );

        }

}
