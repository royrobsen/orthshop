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
                if( $item['parentRef'] == $pid ) {
                    $op[$item['id']] = array(
                        'id' => $item['id'],
                        'catName' => $item['categoryName'],
                        'parentId' => $item['parentRef']
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