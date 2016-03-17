<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Orth\IndexBundle\Entity\Articles;
use Orth\IndexBundle\Entity\ShoppingCart;
use Orth\IndexBundle\Entity\ArticleImages;
use Symfony\Component\HttpFoundation\Session\Session;

class ShoppingCartRepository extends EntityRepository
{
    public function getCartItems($user, $cookie = NULL)
    {
        
        if(is_object($user)) {
                       
            $cookieItems = $this->getCartItemsBySession($cookie);
            
            if ($user->getUserGroup() == 5 ) {
                
                $items = $cookieItems;
                
            } else {
            
                $this->updateSessionToUser($cookieItems, $user);

                $items = $this->getCartItemsByUser($user);
                
            }
        } else {
            
            $items = $this->getCartItemsBySession($cookie);
            
        }
        
        return $items;
        
    }
    
    public function getCartItemsByUser($user)
    {
        
        $cartQuery = $this->findBy(array('userRef' => $user->getId()));
        
        return $cartQuery;
        
    }
    
    public function getSingleCartItemByUser($id)
    {
        
        $cartQuery = $this->findOneBy(array('id' => $id));
        
        return $cartQuery;
        
    }
    
    public function getCartItemsBySession($cookie)
    {
        
            $cartQuery = $this->getEntityManager()
                ->createQuery(
                    'SELECT sc FROM OrthIndexBundle:ShoppingCart sc WHERE sc.sessionId = :sessionId'
                )->setParameter('sessionId', $cookie)->getResult();
        
        return $cartQuery;
        
    }
    
    public function updateSessionToUser($cookieItems, $user)
    {
        $em = $this->_em;
        
        foreach ( $cookieItems as $item ) {
            
            $item->setUserRef($user->getId());
            $item->setCustomerRef($user->getCustomerRef());
            
            $em->persist($item);
            
        }
        
        $em->flush();
        
        return;
        
    }
    
    public function buildCart($items, $user, $cartItems = []) {
        
        foreach ($items as $item) {
            $variantsRepo = $this->getEntityManager()->getRepository('OrthIndexBundle:ArticleSuppliers');
            
            $variants = $variantsRepo->getVariantsById($item->getVarRef());
            
            $query = $this->getEntityManager()->getRepository('OrthIndexBundle:articleAttributeValues')->createQueryBuilder('aav')
                    ->select('aa, aav')
                    ->innerJoin('OrthIndexBundle:ArticleAttributes', 'aa', 'WITH', 'aav.attributeRef = aa.id')
                    ->where('aav.varRef = :string')
                    ->setParameter('string', $item->getVarRef())
                    ->getQuery();
                $varData = $orders = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                $price = $variantsRepo->getSingleCustomPrice($variants->getArticles() ,$user);
                
            $cartItems[] = array('id' => $item->getId(), 'shortName' => $variants->getArticles()->getShortName(), 'articlenumber' => $variants->getSupplierArticleNumber(), 'amount' => $item->getAmount(), 'image' => $variants->getArticles()->getImages(), 'varData' => $varData, 'price' => $price, 'varref' => $item->getVarRef());

        }
        
        return $cartItems;
        
    }
    
    public function updateCart($user, $cookieValue, $updateItems) {
        
        $em = $this->_em;
        
        if(is_object($user)) {
            $this->updateSessionToUser($cookieItems, $user);
        }
        
        foreach ($updateItems as $key => $value) {
            
            $cartItem = $this->getSingleCartItemByUser($key);
            $cartItem->setAmount($value);
            
            $em->persist($cartItem);
            
        }
        
        $em->flush();
        
    }
    
}