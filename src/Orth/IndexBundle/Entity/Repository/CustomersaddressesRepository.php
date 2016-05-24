<?php

namespace Orth\IndexBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class CustomersaddressesRepository extends EntityRepository
{
    
    // returns address by order id
    public function getAddressById($user, $id)
    {
        return $this->findOneBy(array('customerRef' => $user->getCustomerRef(), 'id' => $id));
    }
       
    // returns addresses which are NOT standard invoice or delivery address
    public function getAddressesNonStandard($user)
    {
        return $this->findBy(array('customerRef' => $user->getCustomerRef(), 'primaryAddress' => '0', 'defaultDeliveryAddress' => '0'));
    }
    
    // returns primary address of the customer
    public function getPrimaryAddress($user)
    {
        return $this->findOneBy(array('customerRef' => $user->getCustomerRef(), 'primaryAddress' => '1'));
    }
    
    // returns default delivery address of the customer
    public function getDefaultDeliveryAddress($user)
    {
        return $this->findOneBy(array('customerRef' => $user->getCustomerRef(), 'defaultDeliveryAddress' => '1'));
    }
    
    // returns complete address array (contains primary, default delivery and subarray of other addresses by customer
    public function getAddresses($user)
    {
               
        $array = array( 'primary' => $this->getPrimaryAddress($user),
                        'delivery' => $this->getDefaultDeliveryAddress($user),
                        'others' => $this->getAddressesNonStandard($user)
                      );
        
        return $array;
        
    }
}