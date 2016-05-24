<?php

namespace Orth\IndexBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ArticleSuppliersRepository extends EntityRepository
{
    public function getSingleCustomPrice($article, $user = NULL)
    {

        $price = $this->getSingleNormalPrice($article);
        
        if(is_object($user)) {
            
            $pricesetPrice = $this->getSinglePricesetPrice($article, $user, $price);
                        
            $customerDataPrice = $this->getSingleCustomerDataPrice($article, $user, $price);
            
            if($customerDataPrice != NULL) {
                $price = $customerDataPrice;
            } elseif ($pricesetPrice != NULL) {
                $price = $pricesetPrice;
            }
        }
                
        return $price;
    }
    
    public function getSingleNormalPrice ($article){
        
        $priceQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT asu FROM OrthIndexBundle:ArticleSuppliers asu WHERE asu.articles = :article ORDER BY asu.price ASC'
            )->setParameter('article', $article)->setMaxResults(1)->getOneOrNullResult();
        if($priceQuery == NULL) {
            return 0;
        }
        return $priceQuery->getPrice();

    }
    
    public function getSinglePricesetPrice ($article, $user, $price){
        
           
            $PriceSetQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT ps FROM OrthIndexBundle:Pricesets ps WHERE ps.customerRef = :customer'
            )
            ->setParameter('customer', $user->getCustomerRef())->setMaxResults(1)
            ->getOneOrNullResult();
            
            if($PriceSetQuery != NULL) {
                $PriceSetDataQuery = $this->getEntityManager()
                ->createQuery(
                    'SELECT psd FROM OrthIndexBundle:PricesetData psd WHERE psd.articleRef = :article AND psd.priceset = :priceset ORDER BY psd.price ASC'
                )->setParameter('article', $article->getId())->setParameter('priceset', $PriceSetQuery)->setMaxResults(1)->getOneOrNullResult();

                if($PriceSetDataQuery != NULL AND $PriceSetDataQuery->getPrice() != 0) {
                    return $PriceSetDataQuery->getPrice() * ((100-$PriceSetDataQuery->getDiscount())/100);
                } elseif ($PriceSetDataQuery != NULL AND $PriceSetDataQuery->getPrice() == 0) {
                    return $price * ((100-$PriceSetDataQuery->getDiscount())/100);
                }
            }

    }
    
    public function getSingleCustomerDataPrice($article, $user, $price) {
        
            $customPriceQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT cd FROM OrthIndexBundle:Customerdata cd WHERE cd.article = :article AND cd.customerRef = :customer ORDER BY cd.customPrice ASC'
            )
            ->setParameter('article', $article)
            ->setParameter('customer', $user->getCustomerRef())        
            ->setMaxResults(1)
            ->getOneOrNullResult();

            if($customPriceQuery != NULL AND $customPriceQuery->getCustomPrice() != 0) {
                return $customPriceQuery->getCustomPrice()  * ((100-$customPriceQuery->getCustomDiscount())/100);
            } elseif ($customPriceQuery != NULL AND $customPriceQuery->getCustomPrice() == 0) {
                return $price * ((100-$customPriceQuery->getCustomDiscount())/100);
            }
        
    }
    
    public function getCustomPrice($variant, $user = NULL)
    {

        $price = $this->getNormalPrice($variant);
        
        if(is_object($user)) {
            
            $pricesetPrice = $this->getPricesetPrice($variant, $user, $price);
                        
            $customerDataPrice = $this->getCustomerDataPrice($variant, $user, $price);
            
            if($customerDataPrice != NULL) {
                $price = $customerDataPrice;
            } elseif ($pricesetPrice != NULL) {
                $price = $pricesetPrice;
            }
        }
                
        return $price;
            
    }
    
    public function getNormalPrice ($variant){
        
        $priceQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT asu FROM OrthIndexBundle:ArticleSuppliers asu WHERE asu.id = :variant ORDER BY asu.price ASC'
            )
            ->setParameter('variant', $variant['id'])
            ->setMaxResults(1)
            ->getOneOrNullResult();

        return $priceQuery->getPrice();

    }
    
    public function getPricesetPrice ($variant, $user, $price){
        
            $PriceSetQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT ps FROM OrthIndexBundle:Pricesets ps WHERE ps.customerRef = :customer'
            )
            ->setParameter('customer', $user->getCustomerRef())->setMaxResults(1)->getOneOrNullResult();
            
            if($PriceSetQuery != NULL) {
                $PriceSetDataQuery = $this->getEntityManager()
                ->createQuery(
                    'SELECT psd FROM OrthIndexBundle:PricesetData psd WHERE psd.varRef = :variant AND psd.priceset = :priceset ORDER BY psd.price ASC'
                )->setParameter('variant', $variant['id'])->setParameter('priceset', $PriceSetQuery)->setMaxResults(1)->getOneOrNullResult();

                if($PriceSetDataQuery != NULL AND $PriceSetDataQuery->getPrice() != 0) {
                    return $PriceSetDataQuery->getPrice() * ((100-$PriceSetDataQuery->getDiscount())/100);
                } elseif ($PriceSetDataQuery != NULL AND $PriceSetDataQuery->getPrice() == 0) {
                    return $price * ((100-$PriceSetDataQuery->getDiscount())/100);
                }
            }

    }
    
    public function getCustomerDataPrice($variant, $user, $price) {
        
            $customPriceQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT cd FROM OrthIndexBundle:Customerdata cd WHERE cd.varRef = :variant AND cd.customerRef = :customer ORDER BY cd.customPrice ASC'
            )
            ->setParameter('variant', $variant['id'])->setParameter('customer', $user->getCustomerRef())->setMaxResults(1)->getOneOrNullResult();

            if($customPriceQuery != NULL AND $customPriceQuery->getCustomPrice() != 0) {
                return $customPriceQuery->getCustomPrice()  * ((100-$customPriceQuery->getCustomDiscount())/100);
            } elseif ($customPriceQuery != NULL AND $customPriceQuery->getCustomPrice() == 0) {
                return $price * ((100-$customPriceQuery->getCustomDiscount())/100);
            }
        
    }
    
    public function getNormalPriceDifferenceCount ($article) {
        
        $PriceSetQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT asu FROM OrthIndexBundle:ArticleSuppliers asu WHERE asu.articles = :article GROUP BY asu.price'
            )
            ->setParameter('article', $article)   
            ->getResult();
        
        return count($PriceSetQuery);
        
    }
    
    public function getPricesetDifferenceCount ($article, $user) {
        
            $PriceSetQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT ps FROM OrthIndexBundle:Pricesets ps WHERE ps.customerRef = :customer'
            )
            ->setParameter('customer', $user->getCustomerRef())->setMaxResults(1)
            ->getOneOrNullResult();
            
            if($PriceSetQuery != NULL) {
                $PriceSetDataQuery = $this->getEntityManager()
                ->createQuery(
                    'SELECT psd FROM OrthIndexBundle:PricesetData psd WHERE psd.articleRef = :article AND psd.priceset = :priceset GROUP BY psd.price ORDER BY psd.price ASC'
                )->setParameter('article', $article->getId())->setParameter('priceset', $PriceSetQuery)->getResult();
                if($PriceSetDataQuery != NULL AND $PriceSetDataQuery[0]->getPrice() != 0) {
                    return count($PriceSetDataQuery);
                } elseif ($PriceSetDataQuery != NULL AND $PriceSetDataQuery[0]->getPrice() == 0) {
                    return 0;
                }
            }
               
    }
    
    public function getCustomerDataDifferenceCount($article, $user, $price) {
        
            $customPriceQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT cd FROM OrthIndexBundle:Customerdata cd WHERE cd.article = :article AND cd.customerRef = :customer ORDER BY cd.customPrice ASC'
            )->setParameter('article', $article)->setParameter('customer', $user->getCustomerRef())->getResult();

            if($customPriceQuery != NULL AND $customPriceQuery[0]->getCustomPrice() != 0) {
                return count($customPriceQuery);
            } elseif ($customPriceQuery != NULL AND $customPriceQuery[0]->getCustomPrice() == 0) {
                return 0;
            }
        
    }
    
    public function checkPriceDifferences ($article, $user, $count = true) {
        
        $normalPriceDiffernceCount = $this->getNormalPriceDifferenceCount ($article);
        
        if($normalPriceDiffernceCount == 1 ) {
            $count = false;
        } 
        
        if(is_object($user)) {
            $pricesetDifferenceCount = $this->getPricesetDifferenceCount ($article, $user, $normalPriceDiffernceCount);

            $customerDataDifferenceCount = $this->getCustomerDataDifferenceCount($article, $user, $normalPriceDiffernceCount);

            if ($pricesetDifferenceCount == 1 ) {
                $count = false;
            }
            if ($customerDataDifferenceCount == 1 ) {
                $count = false;
            }
        
        }
        
        return $count;
    }
    
    public function getVariantsById($id) {
        
        $variants = $this->find($id);
        
        return $variants;
        
    }
        
    public function getCustomerDataArticleNumber($variant, $user) {
        
            $customDataQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT cd FROM OrthIndexBundle:Customerdata cd WHERE cd.varRef = :variant AND cd.customerRef = :customer'
            )
            ->setParameter('variant', $variant['id'])->setParameter('customer', $user->getCustomerRef())->setMaxResults(1)->getOneOrNullResult();
            if($customDataQuery != NULL) {
                $customArtNr = $customDataQuery->getCustomArtnr();
            } else {
                $customArtNr = NULL;
            }
            
        return $customArtNr;
    }
    
}