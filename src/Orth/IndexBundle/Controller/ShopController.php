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
        
        $submitted = false;
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
        $article = $em->getRepository('OrthIndexBundle:Articles')->findOneBy(array('id' => $string));
        
        $checkPriceDiff = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->checkPriceDifferences($article, $user);
        //$variants = $article->getVariants()->getValues();
        
        $query = $em->createQuery( "SELECT av, asv FROM OrthIndexBundle:ArticleSuppliers av JOIN av.variantvalues asv WHERE av.articleRef = :articleid")
                ->setParameter(':articleid', $article->getId());
        
        $variants = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $attachments = $em->getRepository('OrthIndexBundle:Attachments')->findBy(array('articleRef' => $article->getId()));
        
        $curCat = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $article->getCatRef()));
        $parentCat = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $curCat->getParentId()));
        $grandparentCat = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $parentCat->getParentId()));
                
        $varTitle = [];
            $i = 0;
        
            foreach ($variants[0]['variantvalues'] as $attRef) {
                $varQuery = $em->getRepository('OrthIndexBundle:ArticleAttributes')->find(array('id' => $attRef['attributeRef']));
                $varTitle[$i]['attrName'] = $varQuery->getAttributeName();
                $i++;
            }
        
        $images = $article->getImages()->getValues();

        if ( $request->request->all() != NULL ) {
            
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
            $submitted = true;
        }
                
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
        
            foreach ($variants as &$value) {
                $price = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->getCustomPrice($value, $user);
                $value['price'] = $price;
            }

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

            return $this->render('OrthIndexBundle:Shop:product.html.twig', array('priceDiff' => $checkPriceDiff,'curCat' => $curCat, 'parent' => $parentCat, 'grandparent' => $grandparentCat,'categories' => $custCategories,'article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images, 'submitted' => $submitted,'attachments' => $attachments));

        }
        return $this->render('OrthIndexBundle:Shop:product.html.twig', array('priceDiff' => $checkPriceDiff,'curCat' => $curCat, 'parent' => $parentCat, 'grandparent' => $grandparentCat,'article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images,'submitted' => $submitted, 'attachments' => $attachments), $response);

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
    
    public function sucheAction(Request $request, $page = 0, $pageOffset = 0, $categories = []) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');
        
        $em = $this->getDoctrine()->getManager();
        
        if ($request->query->get('p')) {
            $page = $request->query->get('p');
            $pageOffset = ($page - 1) * 12;
        }
        
        $catId = $request->query->get('cid');
                
        $category = $request->query->get('c');
        $searchTerm = $request->query->get('q');

        $categoryPath = $em->getRepository('OrthIndexBundle:Categories')->getCategoryPath($category);
        
        $finder = $this->container->get('fos_elastica.finder.search.article');
        $query =  $em->getRepository('OrthIndexBundle:Articles')->getArticleQuery($user, $searchTerm, $page, $pageOffset, $category, $finder);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $articles = $finder->find($query);
        $query->setSize(100);
        $articleCatgeories = $finder->find($query);
        $totalpages = ceil(count($finder->find($query))/12);

        foreach ($articles as $article) {
            // returns actual price for each article e.g. normalprice, priceset price or customer price
            $price = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->getSingleCustomPrice($article, $user);
            // returns true or false ( true if there are price differences )
            $checkPriceDiff = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->checkPriceDifferences($article, $user);
            
            $article->setShowedPrice($price);
            $article->setPriceDiff($checkPriceDiff);

        }
       
        foreach ($articleCatgeories as $articleCategory) {
            $categories[] = $articleCategory->getCatRef();
        }

        $cats = array_count_values($categories);
        $testCat = $cats;
        
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
        
        $test = [];
        foreach ($testCat as $key=>$value) {

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery('SELECT c, c1, c2 FROM OrthIndexBundle:Categories c LEFT JOIN c.parent c1 LEFT JOIN c1.parent c2 WHERE c.id = :id')->setParameter('id', $key)->getResult();

            $test[] = array('id' => $query[0]->getId(), 'parentId' => $query[0]->getParentId(),'catName' => $query[0]->getCategoryName(), 'anzahl' => $value);
            if($query[0]->getParent() != NULL) {
                $test[] = array('id' => $query[0]->getParent()->getId(), 'parentId' => $query[0]->getParent()->getParentId(),'catName' => $query[0]->getParent()->getCategoryName(), 'anzahl' => 0);
            
            if($query[0]->getParent()->getParent() != NULL) {
                $test[] = array('id' => $query[0]->getParent()->getParent()->getId(), 'parentId' => $query[0]->getParent()->getParent()->getParentId(),'catName' => $query[0]->getParent()->getParent()->getCategoryName(), 'anzahl' => 0);
            }}
        }
        $mainCat = buildTree( array_unique($test, SORT_REGULAR) );
  
        return $this->render('OrthIndexBundle:Shop:kategorien.html.twig', array('categoryPath' => $categoryPath, 'articles' => $articles, 'page' => $page, 'totalpages' => $totalpages, 'categories' => $mainCat));
        
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

    public function miniCartAction(Request $request, $cart = NULL, $cartItems = []) {
                    
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $shoppingCart = $em->getRepository('OrthIndexBundle:ShoppingCart');

        if ($request->cookies->get('OrthCookie') != NULL ) {
            
            $cookieValue = $request->cookies->get('OrthCookie');
            $cart = $shoppingCart->getCartItems($user, $cookieValue);
            
        }

        if ( $cart != NULL ) {

            $cartItems = $shoppingCart->buildCart($cart, $user);

        }

        return $this->render('OrthIndexBundle:Shop:minicart.html.twig', array('cart' => $cartItems));
        
    }  
    
    public function removeitemAction(Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');
        $em = $this->getDoctrine()->getManager();
        
        $request = $this->container->get('request'); 
        $varRef = $request->query->get('varRef');
        
        if ($request->cookies->get('OrthCookie') != NULL ) {
                        
            $cookieValue = $request->cookies->get('OrthCookie');
            
            $item = $em->getRepository('OrthIndexBundle:ShoppingCart')->findOneBy(array('sessionId' => $cookieValue, 'varRef' => $varRef));
            
        } elseif ($request->cookies->get('OrthCookie') == NULL AND $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $item = $em->getRepository('OrthIndexBundle:ShoppingCart')->findOneBy(array('userRef' => $user->getId(), 'varRef' => $varRef));
        }
       
            if($item == NULL){
                    $user = $this->get('security.token_storage')->getToken()->getUser();
                    $item = $em->getRepository('OrthIndexBundle:ShoppingCart')->findOneBy(array('userRef' => $user->getId(), 'varRef' => $varRef));
                }

            $em->remove($item);
            $em->flush();
            
            $response = array("code" => 100, "success" => true);
            
            return new Response(json_encode($response)); 
    }
    
        public function shoppingCartAction(Request $request, $cart = NULL, $cartItems = []) {
            
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();
            
            $shoppingCart = $em->getRepository('OrthIndexBundle:ShoppingCart');
            
            if ($request->cookies->get('OrthCookie') != NULL ) {
                $cookieValue = $request->cookies->get('OrthCookie');
                $cart = $shoppingCart->getCartItems($user, $cookieValue);
            }
            
            if ($request->request->get('article') != NULL) {
                $updateItems = $request->request->get('article');
                $shoppingCart->updateCart($user, $cookieValue, $updateItems);
            }
        
            if ( $cart != NULL ) {
                
                $cartItems = $shoppingCart->buildCart($cart, $user);
               
            }

        return $this->render('OrthIndexBundle:Shop:cart.html.twig', array('cart' => $cartItems));
        
    }  
    
}
