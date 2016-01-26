<?php

namespace Orth\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orth\IndexBundle\Entity\Articles;
use Orth\IndexBundle\Entity\ShoppingCart;
use Orth\IndexBundle\Entity\ArticleImages;
use Orth\IndexBundle\Entity\Customerdata;
use Orth\IndexBundle\Entity\Customcategory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Orth\IndexBundle\Form\Type\SearchType;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

class CustomerShopController extends Controller
{  
    
    public function bestellsystemAction(Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $page = 0;
        $pageOffset = 0;
        
        if ($request->query->get('p')) {
            $page = $request->query->get('p');
            $pageOffset = ($page - 1) * 12;
        }
        
        $finder = $this->container->get('fos_elastica.finder.search.article');
        $boolQuery = new \Elastica\Query\BoolQuery();
        $catid = "";       
        if ($request->query->get('c') != NULL) {
            $catid = $request->query->get('c');
        $categoryQuery = new \Elastica\Query\Terms();
        $categoryQuery->setTerms('id', array($catid));
        $boolQuery->addMust($categoryQuery);
        }

        if($request->query->get('q')) {
            
            $searchTerm = $request->query->get('q');
        
            $fieldQuery = new \Elastica\Query\Match();
            $fieldQuery->setFieldQuery('allcustField', $searchTerm);
            $fieldQuery->setFieldOperator('allcustField', 'AND');
            $fieldQuery->setFieldMinimumShouldMatch('allcustField', '90%');
            $fieldQuery->setFieldAnalyzer('allcustField', 'custom_search_analyzer');
            $boolQuery->addMust($fieldQuery);
            
        } else {
            
            $fieldQuery = new \Elastica\Query\MatchAll();
            $boolQuery->addMust($fieldQuery);
            
        }
        
        $fieldQuery = new \Elastica\Query\Match();
        $fieldQuery->setFieldQuery('customerRef',$user->getCustomerRef());
        $boolQuery->addMust($fieldQuery);
        
        
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
        

        
       
        $query = new \Elastica\Query();
        $query->setQuery($boolQuery);
        $query->setSize(10000);
        $articleCatgeories = $finder->find($query);
        $totalpages = ceil(count($finder->find($query))/12);
        $query->setSize(12);
        $query->setFrom($pageOffset);        
        $articles = $finder->find($query);

        $custCat = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef(), 'customerRef' => $user->getCustomerRef()));
        $array = [];
        foreach ($custCat as $cat) {
            if($cat->getParentRef() == NULL) {
            $visibleCat = $em->getRepository('OrthIndexBundle:UserPermissions')->findOneBy(array('custcatRef' => $cat->getId(), 'userRef' => $user->getID()));
                if ( $visibleCat != NULL AND $visibleCat->getPermStatus() == 1 ) {
                $array[] = array('id' => $cat->getId(), 'catName' => $cat->getCategoryName(), 'parentRef' => $cat->getParentRef(), 'anzahl' => 0);
                }
            } else {
                $array[] = array('id' => $cat->getId(), 'catName' => $cat->getCategoryName(), 'parentRef' => $cat->getParentRef(), 'anzahl' => 0);
            }
            
        }
        
        function buildTree( $ar, $pid = null ) {
            $op = array();
            foreach( $ar as $item ) {
                if( $item['parentRef'] == $pid ) {
                    $op[$item['id']] = array(
                        'id' => $item['id'],
                        'catName' => $item['catName'],
                        'parentId' => $item['parentRef'],
                        'anzahl' => $item['anzahl']
                    );
                    // using recursion
                    $children =  buildTree( $ar, $item['id'] );
                    if( $children ) {
                        $op[$item['id']]['children'] = $children;
                    }
                }
            }
            return $op;
        }

        $mainCat = buildTree( $array );

            foreach ($articles as $article) {

                $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('article' => $article->getId(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()), array('customPrice' => 'ASC'));
                if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                    $article->setShowedPrice($customerData->getCustomPrice());
                } else {
                     $variantData = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->findOneBy(array('articleRef' => $article->getId()), array('price' => 'ASC'));
                     $article->setShowedPrice($variantData->getPrice());
                }
                
            }
        
        return $this->render('OrthIndexBundle:CustomShop:bestellsystem.html.twig', array('cid' => $catid, 'articles' => $articles, 'page' => $page, 'totalpages' => $totalpages, 'categories' => $mainCat));
        
        } 
        
    public function catsucheAction($catid, Request $request) {
        
        $em = $this->getDoctrine()->getManager();
                
        $page = 0;
        $pageOffset = 0;
        
        if ($request->query->get('p')) {
            $page = $request->query->get('p');
            $pageOffset = ($page - 1) * 12;
        }
        
        $finder = $this->container->get('fos_elastica.finder.search.article');
        $boolQuery = new \Elastica\Query\BoolQuery();
        
        if ($request->query->get('q') !== NULL) {
            $searchTerm = $request->query->get('q');
            $fieldQuery = new \Elastica\Query\MultiMatch();
            $fieldQuery->setFields(array('shortName', 'shortDescription', 'longDescription'));
            $fieldQuery->setAnalyzer('custom_search_analyzer');
            $fieldQuery->setOperator('AND');
            $fieldQuery->setQuery($searchTerm);
            $boolQuery->addMust($fieldQuery);
        }        
        
        $categoryQuery = new \Elastica\Query\Terms();
        $categoryQuery->setTerms('catRef', array($catid));
        $boolQuery->addMust($categoryQuery);
        
        $query = new \Elastica\Query();
        $query->setQuery($boolQuery);
        $query->setSize(10000000);
        $totalpages = ceil(count($finder->find($query))/12);
        $query->setSize(12);
        $query->setFrom($pageOffset);        
        $articles = $finder->find($query);

        return $this->render('OrthIndexBundle:Shop:kategorien.html.twig', array('articles' => $articles, 'page' => $page, 'totalpages' => $totalpages));
        
       
        }   
        
    public function addarticleAction(Request $request) {
        
        $id = $request->query->get('varRef');
        if($request->query->get('catref')) {
            $catref = $request->query->get('catref');
        }
        $customArtnr = $request->query->get('customArtnr');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();

        $variants = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->findBy(array('articleRef' => $id));
        $article = $em->getRepository('OrthIndexBundle:Articles')->findOneById($id);
        
        foreach ($variants as $variant) {

            $customerData = new Customerdata();
            if($request->query->get('catref')) {
                $custcat = $em->getRepository('OrthIndexBundle:Customcategory')->findOneById($request->query->get('catref'));
                $customerData->setCustcat($custcat);
            } else {
                $customerData->setCustomCatRef(1);  
            }
            $customerData->setCustomArtnr($customArtnr);
            $customerData->setCustomPrice(0);
            $customerData->setCustomerRef($user->getCustomerRef());
            $customerData->setUserRef($user->getId());
            $customerData->setVarRef($variant->getId());
            $customerData->setCustomDiscount(0);
            $customerData->setArticle($article);   
            
            $em->persist($customerData);
            $em->flush();
           
        }
        
        $this->container->get('fos_elastica.object_persister.search.article')->replaceOne($article);
         
        $response = array("code" => 100, "success" => true);
            
        return $this->redirectToRoute('orth_customershop_bestellsystem');
    }     

    public function deletearticleAction(Request $request) {
        
        $id = $request->query->get('varRef');
        if($request->query->get('cid')) {
            $catref = $request->query->get('cid');
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        
        $article = $em->getRepository('OrthIndexBundle:Articles')->findOneBy(array('id' => $id));
        
        $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findBy(array('article' => $article->getId(), 'customCatRef' => $catref));
        dump($article);
        foreach ($customerData as $varEntity) {
            dump($varEntity);
            $em->remove($varEntity);
            $em->flush();
           
        }
        
        $this->container->get('fos_elastica.object_persister.search.article')->replaceOne($article);
         
        $response = array("code" => 100, "success" => true);
            
        return new Response(json_encode($response));  
    }    
    
        public function miniCartAction(Request $request) {
            $cart = NULL;
        if ($request->cookies->get('OrthCookie') != NULL ) {
            $cookieValue = $request->cookies->get('OrthCookie');
        
            
            $em = $this->getDoctrine()->getManager();  
            
            $user = $this->get('security.token_storage')->getToken()->getUser();
            
                    $securityContext = $this->container->get('security.authorization_checker');
                    
                    if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                        
                        $updateCarts = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('sessionId' => $cookieValue));
                        
                        if ($updateCarts != NULL) {
                            foreach ($updateCarts as $updateCart) {
                                $updateCart->setUserRef($user->getId());
                                $updateCart->setCustomerRef($user->getCustomerRef());

                                $em->persist($updateCart);
                                $em->flush();
                            }
                        }
                        
                        $cart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('userRef' => $user->getId()));
                        
                    } else {
                        $cart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('sessionId' => $cookieValue));
                    }
        }
            $cartItems = [];
            if ( $cart != NULL ) {

                foreach ($cart as $item) {
                    $em = $this->getDoctrine()->getManager();
                    $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($item->getVarRef());
                    //$varData = $em->getRepository('OrthIndexBundle:articleAttributeValues')->findBy(array('varRef' => $item->getVarRef())); 
                    
                    $repository = $this->getDoctrine()->getRepository('OrthIndexBundle:articleAttributeValues');
                
                    $query = $repository->createQueryBuilder('aav')
                        ->select('aa, aav')
                        ->innerJoin('OrthIndexBundle:ArticleAttributes', 'aa', 'WITH', 'aav.attributeRef = aa.id')
                        ->where('aav.varRef = :string')
                        ->setParameter('string', $item->getVarRef())
                        ->getQuery();
                    $varData = $orders = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                    
                    $cartItems[] = array('shortName' => $product->getArticles()->getShortName(), 'articlenumber' => $product->getSupplierArticleNumber(), 'amount' => $item->getAmount(), 'image' => $product->getArticles()->getImages(), 'varData' => $varData, 'price' => $product->getPrice(), 'varref' => $item->getVarRef());
                }
            }

        return $this->render('OrthIndexBundle:Shop:minicart.html.twig', array('cart' => $cartItems));
        
    }  
    
    public function removeitemAction(Request $request) {
        
        if ($request->cookies->get('OrthCookie') != NULL ) {
            
            $request = $this->container->get('request'); 
            $varRef = $request->query->get('varRef');
            
            $cookieValue = $request->cookies->get('OrthCookie');
            
            $em = $this->getDoctrine()->getManager();

            $item = $em->getRepository('OrthIndexBundle:ShoppingCart')->findOneBy(array('sessionId' => $cookieValue, 'varRef' => $varRef));
            
            if($item == NULL){
                $user = $this->get('security.token_storage')->getToken()->getUser();
                $item = $em->getRepository('OrthIndexBundle:ShoppingCart')->findOneBy(array('userRef' => $user->getId(), 'varRef' => $varRef));
            }
            
            $em->remove($item);
            $em->flush();
            
            $response = array("code" => 100, "success" => true);
            
            return new Response(json_encode($response)); 
            
        }
    }
    
        public function shoppingCartAction(Request $request) {
            $cart = NULL;
        if ($request->cookies->get('OrthCookie') != NULL ) {
            $cookieValue = $request->cookies->get('OrthCookie');
        
            
            $em = $this->getDoctrine()->getManager();  
            
            $user = $this->get('security.token_storage')->getToken()->getUser();
            
                    $securityContext = $this->container->get('security.authorization_checker');
                    
                    if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                        
                        $updateCarts = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('sessionId' => $cookieValue));
                        
                        if ($updateCarts != NULL) {
                            foreach ($updateCarts as $updateCart) {
                                $updateCart->setUserRef($user->getId());
                                $updateCart->setCustomerRef($user->getCustomerRef());

                                $em->persist($updateCart);
                                $em->flush();
                            }
                        }
                        
                        $cart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('userRef' => $user->getId()));
                        
                    } else {
                        $cart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('sessionId' => $cookieValue));
                    }
        }
            $cartItems = [];
            if ( $cart != NULL ) {

                foreach ($cart as $item) {
                    $em = $this->getDoctrine()->getManager();
                    $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($item->getVarRef());
                    //$varData = $em->getRepository('OrthIndexBundle:articleAttributeValues')->findBy(array('varRef' => $item->getVarRef())); 
                    
                    $repository = $this->getDoctrine()->getRepository('OrthIndexBundle:articleAttributeValues');
                
                    $query = $repository->createQueryBuilder('aav')
                        ->select('aa, aav')
                        ->innerJoin('OrthIndexBundle:ArticleAttributes', 'aa', 'WITH', 'aav.attributeRef = aa.id')
                        ->where('aav.varRef = :string')
                        ->setParameter('string', $item->getVarRef())
                        ->getQuery();
                    $varData = $orders = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                    
                    $cartItems[] = array('shortName' => $product->getArticles()->getShortName(), 'articlenumber' => $product->getSupplierArticleNumber(), 'amount' => $item->getAmount(), 'image' => $product->getArticles()->getImages(), 'varData' => $varData, 'price' => $product->getPrice(), 'varref' => $item->getVarRef());
                }
            }

        return $this->render('OrthIndexBundle:Shop:cart.html.twig', array('cart' => $cartItems));
        
    }  
    
}
