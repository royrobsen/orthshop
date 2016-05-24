<?php

namespace Orth\IndexBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class UsersRepository extends EntityRepository
{
    // returns all users with same customer ids
    public function getUsersByCustomer($user)
    {
        return $this->findBy(array('customerRef' => $user->getCustomerRef()));
    }

    // returns all users with same customer ids
    public function getOneUserByCustomerAndId($user, $id)
    {
        return $this->findOneBy(array('customerRef' => $user->getCustomerRef(), 'id' => $id));
    }
    
}