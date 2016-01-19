<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Orth\IndexBundle\Entity\Articles;
use Orth\IndexBundle\Entity\ShoppingCart;
use Orth\IndexBundle\Entity\ArticleImages;
use Symfony\Component\HttpFoundation\Session\Session;

class ShoppingCartRepository extends EntityRepository
{
    public function findSumOfShoppingCart()
    {
    }
}