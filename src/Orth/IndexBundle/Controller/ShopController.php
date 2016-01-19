<?php

namespace Orth\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orth\IndexBundle\Entity\Articles;
use Orth\IndexBundle\Entity\Attachments;
use Orth\IndexBundle\Entity\ShoppingCart;
use Orth\IndexBundle\Entity\ArticleImages;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Orth\IndexBundle\Form\Type\SearchType;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

class ShopController extends Controller
{
    public function kategorienAction() {
        
        $articles = array(array('shortName' => 'Test1', 'shortDescription' =>'shortDesc', 'longDescription' => 'longDesc'), array('shortName' => 'Test1', 'shortDescription' =>'shortDesc', 'longDescription' => 'longDesc'));
        return $this->render('OrthIndexBundle:Shop:kategorien.html.twig', array('articles' => $articles));
    }

    public function productAction(Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');
        $response = new Response();
         
        if ($request->cookies->get('OrthCookie') == NULL ) {
            $cookieValue = uniqid();
            $response->headers->setCookie(new Cookie('OrthCookie', uniqid()));
        } 
        else { 
            $cookieValue = $request->cookies->get('OrthCookie');
        }
       
        $string = $request->query->get('q');
        
        $article = new Articles();      
        
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('OrthIndexBundle:Articles')->findOneBy(array('articleNumber' => $string));
        
        //$variants = $article->getVariants()->getValues();
        
        $query = $em->createQuery( "SELECT av, asv FROM OrthIndexBundle:ArticleSuppliers av JOIN av.variantvalues asv WHERE av.articleRef = :articleid")
                ->setParameter(':articleid', $article->getId());
        
        $variants = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $attachments = $em->getRepository('OrthIndexBundle:Attachments')->findBy(array('articleRef' => $article->getId()));
        
        $varTitle = [];
            $i = 0;
        
            foreach ($variants[0]['variantvalues'] as $attRef) {
                $varQuery = $em->getRepository('OrthIndexBundle:ArticleAttributes')->find(array('id' => $attRef['attributeRef']));
                $varTitle[$i]['attrName'] = $varQuery->getAttributeName();
                $i++;
            }
        
        $images = $article->getImages()->getValues();

        if ( $productsToCart = $request->request->all() != NULL ) {
            
            $productsToCart = $request->request->all();
            $completeCart = array();

            foreach ($productsToCart as $key => $value) {
                
                if($value != "") {

                    
                    if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                        $dbCart =  new ShoppingCart();
                        $dbCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findOneBy(array('userRef' => $user->getId(), 'varRef' => $key));

                        if ( empty($dbCart) ) {
                            
                            $cart = new ShoppingCart();
                            $cart->setVarRef($key);
                            $cart->setAmount($value);
                            $cart->setUserRef($user->getId());
                            $cart->setCustomerRef($user->getCustomerRef());
                            $cart->setSessionId($cookieValue);  
                                                    
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($cart);
                            $em->flush();
                            
                        } else {
                            
                            $updateCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->find($dbCart->getId());
                            $updateCart->setAmount($dbCart->getAmount() + $value);
                            $updateCart->setSessionId($cookieValue);   
                            
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($updateCart);
                            $em->flush();
                            
                        }
                                              
                    } else {
                        
                    $sessionCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findOneBy(array('sessionId' => $cookieValue,'varRef' => $key));
                    
                    if ( empty($sessionCart) ) {
                        
                            $cart = new ShoppingCart();
                            $cart->setVarRef($key);
                            $cart->setAmount($value);
                            $cart->setUserRef(0);
                            $cart->setCustomerRef(0);
                            $cart->setSessionId($cookieValue);  
                            
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($cart);
                            $em->flush();
                    } else {
                            $updateSessionCart = $sessionCart;
                            $updateSessionCart->setAmount($sessionCart->getAmount() + $value);
                                                    
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($updateSessionCart);
                            $em->flush();
                    }
                    } 
                    } 
            } 

        }
                
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
        
            foreach ($variants as &$value) {
                $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('varRef' => $value['id'], 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()));
                $value['customArtnr'] =  $customerData->getCustomArtnr();
                if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                    $value['price'] = $customerData->getCustomPrice();
                }
            }
        }          

        return $this->render('OrthIndexBundle:Shop:product.html.twig', array('article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images, 'attachments' => $attachments), $response);

    }
    
    public function productEditAction (Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');
        
        $string = $request->query->get('q');
        
        $article = new Articles();
        
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('OrthIndexBundle:Articles')->findOneBy(array('articleNumber' => $string));
        
        //$variants = $article->getVariants()->getValues();
        
        $query = $em->createQuery( "SELECT av, asv FROM OrthIndexBundle:ArticleSuppliers av JOIN av.variantvalues asv WHERE av.articleRef = :articleid")
                ->setParameter(':articleid', $article->getId());
        
        $variants = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        
        $varTitle = [];
            $i = 0;
        
            foreach ($variants[0]['variantvalues'] as $attRef) {
                $varQuery = $em->getRepository('OrthIndexBundle:ArticleAttributes')->find(array('id' => $attRef['attributeRef']));
                $varTitle[$i]['attrName'] = $varQuery->getAttributeName();
                $i++;
            }
        
        $images = $article->getImages()->getValues();
         $form = $this->createFormBuilder($article)
                ->add('shortName', 'text', array('label' => false))
                ->add('shortDescription', 'textarea', array('label' => false))
                ->add('longDescription', 'textarea', array('label' => false))
                ->add('catRef', 'textarea', array('label' => false))
                ->add('save', 'submit', array('label' => 'Speichern'))
                ->getForm();  
         
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
        }
        
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $custCat = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef()));
            $array = [];
            foreach ($custCat as $cat) {
                $array[] = array('id' => $cat->getId(), 'catName' => $cat->getCategoryName(), 'parentRef' => $cat->getParentRef(), 'anzahl' => 0);
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

            $custCategories = buildTree( $array);

            return $this->render('OrthIndexBundle:Shop:productEdit.html.twig', array('categories' => $custCategories,'article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images, 'form' => $form->createView()));

        } 
        return $this->render('OrthIndexBundle:Shop:productEdit.html.twig', array('article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images, 'form' => $form->createView()));

    }
    
    public function sucheAction(Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');
        
        $em = $this->getDoctrine()->getManager();
         
        $searchTerm = $request->query->get('q');

        $page = 0;
        $pageOffset = 0;
        
        if ($request->query->get('p')) {
            $page = $request->query->get('p');
            $pageOffset = ($page - 1) * 12;
        }
        
        $catId = $request->query->get('cid');

        $finder = $this->container->get('fos_elastica.finder.search.article');
        $boolQuery = new \Elastica\Query\BoolQuery();
               
        if ($request->query->get('c') != NULL) {
            $catid = $request->query->get('c');
            $categoryQuery = new \Elastica\Query\Terms();
            $categoryQuery->setTerms('catRef', array($catid));
            $boolQuery->addMust($categoryQuery);
        }
        /*
        $fieldQuery = new \Elastica\Query\MultiMatch();
        $fieldQuery->setFields(array('_all'));
        $fieldQuery->setAnalyzer('custom_search_analyzer');
        $fieldQuery->setOperator('AND');
        $fieldQuery->setQuery($searchTerm);
        $boolQuery->addMust($fieldQuery);
                   */
        $fieldQuery = new \Elastica\Query\Match();
        $fieldQuery->setFieldQuery('allField', $searchTerm);
        $fieldQuery->setFieldOperator('allField', 'AND');
        $fieldQuery->setFieldMinimumShouldMatch('allField', '80%');
        $fieldQuery->setFieldAnalyzer('allField', 'custom_search_analyzer');
        $boolQuery->addMust($fieldQuery);

        $query = new \Elastica\Query();
        $query->setQuery($boolQuery);
        $query->setSize(10000);
        $articleCatgeories = $finder->find($query);
        $totalpages = ceil(count($finder->find($query))/12);
        $query->setSize(12);
        $query->setFrom($pageOffset);        
        $articles = $finder->find($query);

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
 
            foreach ($articles as $article) {

                $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('article' => $article->getId(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()), array('customPrice' => 'ASC'));
                if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                    $article->setShowedPrice($customerData->getCustomPrice());
                } else {
                     $variantData = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->findOneBy(array('articleRef' => $article->getId()), array('price' => 'ASC'));
                     $article->setShowedPrice($variantData->getPrice());
                }
                
            }
            
        } else {
            foreach ($articles as $article) {
                $variantData = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->findOneBy(array('articleRef' => $article->getId()), array('price' => 'ASC'));
                $article->setShowedPrice($variantData->getPrice());
            }
        }    

        $categories = [];
        foreach ($articleCatgeories as $articleCategory) {
            $categories[] = $articleCategory->getCatRef();
        }

        $cats = array_count_values($categories);
   
        $array = [];
        foreach ( $cats as $key => $value ) {
            $cat1 = $em->getRepository('OrthIndexBundle:Categories')->findOneById($key); 
            if($cat1 != NULL) {
            $array[] = array('id' => $cat1->getId(), 'catName' => $cat1->getCategoryName(), 'parentId' => $cat1->getParentId(), 'anzahl' => $value);
            if($cat1->getParentId() != NULL) {
                $check = true;
                while ($check) {
                    $cat2 = $em->getRepository('OrthIndexBundle:Categories')->findOneById($cat1->getParentId()); 
                    $array[] = array('id' => $cat2->getId(), 'catName' => $cat2->getCategoryName(), 'parentId' => $cat2->getParentId(), 'anzahl' => $value);
                        if($cat2->getParentId() == NULL) {
                            $check = false;
                        }
                        if($cat2->getParentId() != NULL) {
                            $cat3 = $em->getRepository('OrthIndexBundle:Categories')->findOneById($cat2->getParentId()); 
                            $array[] = array('id' => $cat3->getId(), 'catName' => $cat3->getCategoryName(), 'parentId' => $cat3->getParentId(), 'anzahl' => $value);
                            if($cat3->getParentId() == NULL) {
                                $check = false;
                            }
                            if($cat3->getParentId() != NULL) {
                                $cat4 = $em->getRepository('OrthIndexBundle:Categories')->findOneById($cat3->getParentId()); 
                                $array[] = array('id' => $cat2->getId(), 'catName' => $cat4->getCategoryName(), 'parentId' => $cat4->getParentId(), 'anzahl' => $value);
                                if($cat4->getParentId() == NULL) {
                                    $check = false;
                                }
                            }
                        }
                    }
                }
            }
        }

        function buildTree( $ar, $pid = null ) {
            $op = array();
            foreach( $ar as $item ) {
                if( $item['parentId'] == $pid ) {
                    $op[$item['id']] = array(
                        'id' => $item['id'],
                        'catName' => $item['catName'],
                        'parentId' => $item['parentId'],
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

        return $this->render('OrthIndexBundle:Shop:kategorien.html.twig', array('articles' => $articles, 'page' => $page, 'totalpages' => $totalpages, 'categories' => $mainCat));
        

     }   
        
    public function catsucheAction($catid, Request $request) {
        
        $em = $this->getDoctrine()->getManager();
                $mainCat = [];
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

        return $this->render('OrthIndexBundle:Shop:kategorien.html.twig', array('articles' => $articles, 'page' => $page, 'totalpages' => $totalpages, 'categories' => $mainCat));
        
       
        }   
        
    public function getcartsessiondataAction() {
            
        $session = new Session();
                       
        $response = new Response();
        $response->setContent(json_encode($session->get('cart')));
        
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
        
    }     

        public function miniCartAction(Request $request) {
            $cart = NULL;
          $user = $this->get('security.token_storage')->getToken()->getUser();
            
                    $securityContext = $this->container->get('security.authorization_checker');
        if ($request->cookies->get('OrthCookie') != NULL ) {
            $cookieValue = $request->cookies->get('OrthCookie');
        
            
            $em = $this->getDoctrine()->getManager();  
                              
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
                    
                    $repository = $this->getDoctrine()->getRepository('OrthIndexBundle:ArticleAttributeValues');
                
                    $query = $repository->createQueryBuilder('aav')
                        ->select('aa, aav')
                        ->innerJoin('OrthIndexBundle:ArticleAttributes', 'aa', 'WITH', 'aav.attributeRef = aa.id')
                        ->where('aav.varRef = :string')
                        ->setParameter('string', $item->getVarRef())
                        ->getQuery();
                    $varData = $orders = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                    $price = $product->getPrice();
                    if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                        $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('varRef' => $item->getVarRef(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()));
                        if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                            $price = $customerData->getCustomPrice();
                        }
                    }  
                    
                    $cartItems[] = array('shortName' => $product->getArticles()->getShortName(), 'articlenumber' => $product->getSupplierArticleNumber(), 'amount' => $item->getAmount(), 'image' => $product->getArticles()->getImages(), 'varData' => $varData, 'price' => $price, 'varref' => $item->getVarRef());
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
            
            $user = $this->get('security.token_storage')->getToken()->getUser();
            
        $securityContext = $this->container->get('security.authorization_checker');
        if ($request->cookies->get('OrthCookie') != NULL ) {
            $cookieValue = $request->cookies->get('OrthCookie');
        
            
            $em = $this->getDoctrine()->getManager();  
                                
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
                    $price = $product->getPrice();
                if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                    $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('varRef' => $item->getVarRef(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()));
                    if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                        $price = $customerData->getCustomPrice();
                    }
                }  
                    
                    $cartItems[] = array('shortName' => $product->getArticles()->getShortName(), 'articlenumber' => $product->getSupplierArticleNumber(), 'amount' => $item->getAmount(), 'image' => $product->getArticles()->getImages(), 'varData' => $varData, 'price' => $price, 'varref' => $item->getVarRef());
                }
            }

        return $this->render('OrthIndexBundle:Shop:cart.html.twig', array('cart' => $cartItems));
        
    }  
    
}
