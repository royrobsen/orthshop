<?php

namespace Orth\IndexBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class OrdersRepository extends EntityRepository
{
    // returns order history by logged in user, user has to be logged in
    public function getOrderHistory($user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT o, sum(op.posPrice * op.posAmount) as sumvalue FROM OrthIndexBundle:Orders o JOIN o.positions op WHERE o.customerRef = :customerRef GROUP BY o.id'
            )
            ->setParameter('customerRef', $user->getCustomerRef())
            ->getResult();
    }
    
    public function getOrder($user, $id) 
    {
        
        return $this->getEntityManager()
            ->createQuery(
                'SELECT o,op FROM OrthIndexBundle:Orders o JOIN o.positions op WHERE o.customerRef = :customerRef AND o.id = :id')
                    ->setParameter(':customerRef', $user->getCustomerRef())
                    ->setParameter(':id', $id)
                    ->getResult();
    }
        
}