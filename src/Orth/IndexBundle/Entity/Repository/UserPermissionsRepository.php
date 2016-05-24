<?php

namespace Orth\IndexBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class UserPermissionsRepository extends EntityRepository
{
    // return single userpermission by user and customcategory
    public function getUsersPermission($id, $custId)
    {
        return $this->findOneBy(array('userRef' => $id, 'custcatRef' => $custId));
    }
    
}