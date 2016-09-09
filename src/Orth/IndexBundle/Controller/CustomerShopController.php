<?php

namespace Orth\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orth\IndexBundle\Entity\Articles;
use Orth\IndexBundle\Entity\ShoppingCart;
use Orth\IndexBundle\Entity\ArticleImages;
use Orth\IndexBundle\Entity\Customerdata;
use Orth\IndexBundle\Entity\Customcategory;
use Orth\IndexBundle\Entity\Approvals;
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

        $finder = $this->container->get('fos_elastica.finder.search.articles');

        $category = "";
        if ($request->query->get('c') != NULL) {
            $category = $request->query->get('c');
        }
        $searchTerm = "";
        if($request->query->get('q')) {
            $searchTerm = $request->query->get('q');
        }

        $colors = [];
        $articles =  $em->getRepository('OrthIndexBundle:Customerdata')->getArticles($user, $searchTerm, $page, $pageOffset, $category, $colors, $finder);
        $totalpages = $finder->getSearch()->count($articles['rQuery']);
        $custCat = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef(), 'customerRef' => $user->getCustomerRef()));
        $array = [];
        foreach ($custCat as $cat) {
            if($cat->getParentId() == NULL) {
            $visibleCat = $em->getRepository('OrthIndexBundle:UserPermissions')->findOneBy(array('custcatRef' => $cat->getId(), 'userRef' => $user->getID()));
                if ( $visibleCat != NULL AND $visibleCat->getPermStatus() == 1 ) {
                $array[] = array('id' => $cat->getId(), 'catName' => $cat->getCategoryName(), 'parentId' => $cat->getParentId(), 'anzahl' => 0);
                }
            } else {
                $array[] = array('id' => $cat->getId(), 'catName' => $cat->getCategoryName(), 'parentId' => $cat->getParentId(), 'anzahl' => 0);
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

            foreach ($articles['articles'] as $article) {

                //$customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findBy(array('article' => $article->getId(), 'customerRef' => $user->getCustomerRef()), array('customPrice' => 'ASC'));
                $query = $em->createQuery( "SELECT cd FROM OrthIndexBundle:CustomerData cd WHERE cd.article = :articleRef AND cd.customerRef = :customerRef GROUP BY cd.customCatRef ORDER BY cd.customPrice ASC")
                        ->setParameter('customerRef', $user->getCustomerRef())
                        ->setParameter('articleRef', $article->getId());

                $customerData = $query->getResult();

                $price = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->getSingleCustomPrice($article->getId(), $user);
                $checkPriceDiff = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->checkPriceDifferences($article, $user);

                $article->setShowedPrice($price);
                $article->setPriceDiff($checkPriceDiff);

                $customCategories = [];
                foreach ( $customerData as $customerDataEntity ) {
                  $customCategories[] = $customerDataEntity->getCustCat();
                }

                $article->setCustomCategoryResultsArray($customCategories);

            }

        return $this->render('OrthIndexBundle:CustomShop:bestellsystem.html.twig', array('cid' => $category, 'articles' => $articles['articles'], 'page' => $page, 'totalpages' => $totalpages, 'categories' => $mainCat));

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
        $custom = $request->query->get('custom');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        //$variants = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->findBy(array('articleRef' => $id));
        $article = $em->getRepository('OrthIndexBundle:Articles')->findOneById($id);
        $article->getCustdata();
        foreach ($custom as $key => &$value) {


          if(isset($value['checked'])) {
            $customerData = new Customerdata();
            if($request->query->get('catref')) {
                $custcat = $em->getRepository('OrthIndexBundle:Customcategory')->findOneById($request->query->get('catref'));
                $customerData->setCustcat($custcat);
            } else {
                $customerData->setCustomCatRef(1);
            }
            $customerData->setCustomArtnr($value['articleNumber']);
            $customerData->setCustomPrice(0);
            $customerData->setCustomerRef($user->getCustomerRef());
            $customerData->setUserRef($user->getId());
            $customerData->setVarRef($key);
            $customerData->setCustomDiscount(0);
            $customerData->setArticle($article);

            $em->persist($customerData);
            $em->flush();
          }
        }

        $this->container->get('fos_elastica.object_persister.search.articles')->replaceOne($article);

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

    public function customproductAction($productslug, Request $request) {

        $slugIds = explode('-', $productslug);
        $submitted = false;
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');
        $response = new Response();

        if ($request->cookies->get('OrthCookie') == false ) {
            $cookieValue = uniqid();
            $response->headers->setCookie(new Cookie('OrthCookie', uniqid()));
            $response->sendHeaders();
            $response->send();
        }
        else {
            $cookieValue = $request->cookies->get('OrthCookie');
        }

        $article = new Articles();

        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('OrthIndexBundle:Articles')->findOneBy(array('id' => $slugIds[1]));

        if($user != 'anon.' && $article->getCustomized() != 0 && $article->getCustomized() != $user->getCustomerRef()) {
            throw new \Exception();
        } elseif ($user == 'anon.' && $article->getCustomized() != 0) {
            throw new \Exception();
        }

        $categoryPath = $em->getRepository('OrthIndexBundle:Customcategory')->getCustomCategoryPath($slugIds[0]);
        $checkPriceDiff = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->checkPriceDifferences($article, $user);
        //$variants = $article->getVariants()->getValues();

        //$query = $em->createQuery( "SELECT av, asv FROM OrthIndexBundle:ArticleSuppliers av LEFT JOIN av.variantvalues asv WHERE av.articles = :articleid")
        //        ->setParameter('articleid', $article->getId());

        //$variants = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $query = $em->createQuery( "SELECT cd.varRef FROM OrthIndexBundle:CustomerData cd WHERE cd.customerRef = :customerRef AND cd.article = :articleRef AND cd.customCatRef = :category")
                ->setParameter('customerRef', $user->getCustomerRef())
                ->setParameter('articleRef', $article)
                ->setParameter('category', $slugIds[0]);

        $customData = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $varArray = array_map('current', $customData);
        $query = $em->createQuery( "SELECT av, asv, cd FROM OrthIndexBundle:ArticleSuppliers av LEFT JOIN av.variantvalues asv LEFT JOIN av.custdata cd WHERE av.articles = :articleid AND av.id IN (:varArray) AND cd.customerRef = :customerRef AND cd.article = :articleid AND cd.customCatRef = :category")
                ->setParameter('articleid', $article)
                ->setParameter('varArray', $varArray)
                ->setParameter('customerRef', $user->getCustomerRef())
                ->setParameter('category', $slugIds[0]);

        $variants = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $attachments = $em->getRepository('OrthIndexBundle:Attachments')->findBy(array('articleRef' => $article->getId()));

//        $curCat = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $article->getCatRef()));
//        $parentCat = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $curCat->getParentId()));
//        $grandparentCat = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $parentCat->getParentId()));

        $varTitle = [];
            $i = 0;
            if(isset($variants[0])) {
                foreach ($variants[0]['variantvalues'] as $attRef) {
                    $varQuery = $em->getRepository('OrthIndexBundle:ArticleAttributes')->find(array('id' => $attRef['attributeRef']));
                    $varTitle[$i]['attrName'] = $varQuery->getAttributeName();
                    $i++;

                }
            } else {
                $varTitle[0] = array('attrName' => "test");
            }
        $query = $em->createQuery( "SELECT ai.picName, asu.desc1, av.attributeValue, av.attributeRef, ai.id, ai.articleRef, ai.imgCrc32 FROM OrthIndexBundle:ArticleImages ai LEFT JOIN ai.articles asu LEFT JOIN asu.variantvalues av WHERE ai.productRef = :articleid AND ai.articleRef IN (:varArray) GROUP BY ai.picName ORDER BY av.attributeRef ASC")
                ->setParameter('articleid', $article->getId())
                ->setParameter('varArray', $varArray);

        $images = $query->getResult();

        foreach ($images as $image) {

            if (!file_exists('http://127.0.0.1:8000/images/product/nw/'. $image['picName'])) {
                $image['picName'] = 'nopicture.jpg';
        }

        }

        if ( $request->request->all() != NULL ) {

            $productsToCart = $request->request->get('var');
            $completeCart = array();

            foreach ($productsToCart as $key=>$value) {

                if($value[2] != "") {

                    if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

                        $dbCart =  new ShoppingCart();
                        $dbCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findOneBy(array('userRef' => $user->getId(), 'varRef' => $value[0], 'customdataRef' => $value[1]));

                            if ( empty($dbCart) ) {

                                $cart = new ShoppingCart();
                                $cart->setVarRef($value[0]);
                                $cart->setCustomdataRef($value[1]);
                                $cart->setAmount($value[2]);
                                $cart->setPositionsText($value[3]);
                                $cart->setUserRef($user->getId());
                                $cart->setCustomerRef($user->getCustomerRef());
                                $cart->setSessionId($cookieValue);
                                $cart->setApprovalRef(0);


                                $em = $this->getDoctrine()->getManager();
                                $em->persist($cart);
                                $em->flush();

                            } else {

                                $updateCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->find($dbCart->getId());
                                $updateCart->setAmount($dbCart->getAmount() + $value[2]);
                                $updateCart->setPositionsText($dbCart->getPositionsText() . " " . $value[3]);
                                $updateCart->setSessionId($cookieValue);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($updateCart);
                                $em->flush();

                            }

                    } else {

                    $sessionCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findOneBy(array('sessionId' => $cookieValue,'varRef' => $key));

                    if ( empty($sessionCart) ) {

                            $cart = new ShoppingCart();
                            $cart->setVarRef($value[0]);
                            $cart->setAmount($value[2]);
                            $cart->setPositionsText($value[3]);
                            $cart->setUserRef(0);
                            $cart->setCustomerRef(0);
                            $cart->setSessionId($cookieValue);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($cart);
                            $em->flush();
                    } else {
                            $updateSessionCart = $sessionCart;
                            $updateSessionCart->setAmount($sessionCart->getAmount() + $value[2]);
                            $updateSessionCart->setPositionsText($sessionCart->getPositionsText() . " " . $value[3]);

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
                $customArtnr = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->getCustomerDataArticleNumber($value, $user);
                $value['price'] = $price;
                $value['customArtnr'] = $customArtnr;
            }

            $custCat = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef()));
            $array = [];
            foreach ($custCat as $cat) {
                $array[] = array('id' => $cat->getId(), 'catName' => $cat->getCategoryName(), 'parentId' => $cat->getParentId(), 'anzahl' => 0);
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

            $custCategories = buildTree( $array);

            return $this->render('OrthIndexBundle:CustomShop:customproduct.html.twig', array('priceDiff' => $checkPriceDiff,'categories' => $custCategories,'article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images, 'submitted' => $submitted,'attachments' => $attachments, 'categoryPath' => $categoryPath));

        }
        return $this->render('OrthIndexBundle:CustomShop:customproduct.html.twig', array('priceDiff' => $checkPriceDiff,'article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images,'submitted' => $submitted, 'attachments' => $attachments, 'categoryPath' => $categoryPath), $response);

    }

    public function requireapprovalAction(Request $request, $cart = NULL, $cartItems = []) {

      $user = $this->get('security.token_storage')->getToken()->getUser();

      $em = $this->getDoctrine()->getManager();

      $shoppingCart = $em->getRepository('OrthIndexBundle:ShoppingCart');

      if ($request->cookies->get('OrthCookie') != NULL ) {

          $cookieValue = $request->cookies->get('OrthCookie');
          $cart = $shoppingCart->getCartItems($user, $cookieValue);

      }
      foreach ($cart as $cartItem) {

        $new_approval = new Approvals();
        $new_approval->setRequestuser( $user );
        $new_approval->setApproved(0);
        $em->persist($new_approval);
        $em->flush();
        $cartItem->setApprovalRef($new_approval->getId());
        $em->persist($cartItem);
        $em->flush();
      }

      return $this->redirectToRoute('orth_account_approvals');
    }

}
