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

    public function productAction($productslug, Request $request) {

        $productId = explode('-', $productslug);
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
        $article = $em->getRepository('OrthIndexBundle:Articles')->findOneBy(array('id' => $productId[0]));

        if($user != 'anon.' && $article->getCustomized() != 0 && $article->getCustomized() != $user->getCustomerRef()) {
            throw new \Exception();
        } elseif ($user == 'anon.' && $article->getCustomized() != 0) {
            throw new \Exception();
        }

        $categoryPath = $em->getRepository('OrthIndexBundle:Categories')->getCategoryPath($article->getCategory());
        $checkPriceDiff = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->checkPriceDifferences($article, $user);
        //$variants = $article->getVariants()->getValues();

        $query = $em->createQuery( "SELECT av, asv FROM OrthIndexBundle:ArticleSuppliers av LEFT JOIN av.variantvalues asv WHERE av.articles = :articleid")
                ->setParameter('articleid', $article->getId());

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
        $query = $em->createQuery( "SELECT ai.picName, asu.desc1, av.attributeValue, av.attributeRef, ai.id, ai.articleRef, ai.imgCrc32 FROM OrthIndexBundle:ArticleImages ai LEFT JOIN ai.articles asu LEFT JOIN asu.variantvalues av WHERE ai.productRef = :articleid GROUP BY ai.picName ORDER BY av.attributeRef ASC")
                ->setParameter('articleid', $article->getId());

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

                if($value[1] != "") {


                    if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

                        $dbCart =  new ShoppingCart();
                        $dbCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findOneBy(array('userRef' => $user->getId(), 'varRef' => $value[0]));

                            if ( empty($dbCart) ) {

                                $cart = new ShoppingCart();
                                $cart->setVarRef($value[0]);
                                $cart->setAmount($value[1]);
                                $cart->setPositionsText($value[2]);
                                $cart->setUserRef($user->getId());
                                $cart->setCustomerRef($user->getCustomerRef());
                                $cart->setSessionId($cookieValue);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($cart);
                                $em->flush();

                            } else {

                                $updateCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->find($dbCart->getId());
                                $updateCart->setAmount($dbCart->getAmount() + $value[1]);
                                $updateCart->setPositionsText($dbCart->getPositionsText() . " " . $value[2]);
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
                            $cart->setAmount($value[1]);
                            $cart->setPositionsText($value[2]);
                            $cart->setUserRef(0);
                            $cart->setCustomerRef(0);
                            $cart->setSessionId($cookieValue);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($cart);
                            $em->flush();
                    } else {
                            $updateSessionCart = $sessionCart;
                            $updateSessionCart->setAmount($sessionCart->getAmount() + $value[1]);
                            $updateSessionCart->setPositionsText($sessionCart->getPositionsText() . " " . $value[2]);

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

            return $this->render('OrthIndexBundle:Shop:product.html.twig', array('priceDiff' => $checkPriceDiff,'categories' => $custCategories,'article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images, 'submitted' => $submitted,'attachments' => $attachments, 'categoryPath' => $categoryPath));

        }
        return $this->render('OrthIndexBundle:Shop:product.html.twig', array('priceDiff' => $checkPriceDiff,'article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images,'submitted' => $submitted, 'attachments' => $attachments, 'categoryPath' => $categoryPath), $response);

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

            return $this->render('OrthIndexBundle:Shop:productEdit.html.twig', array('categories' => $custCategories,'article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images, 'form' => $form->createView()));

        }
        return $this->render('OrthIndexBundle:Shop:productEdit.html.twig', array('article' => $article, 'variants' => $variants,'varTitle' => $varTitle, 'images' => $images, 'form' => $form->createView()));

    }

    public function sucheAction(Request $request, $category = NULL, $searchTerm = NULL, $childcategory = NULL, $grandchildcategory = NULL, $page = 0, $pageOffset = 0, $categories = [], $orderby = NULL, $colors = NULL) {


        $formDaten = $request->query->get('form');

        $searchTerm = $formDaten['q'];
        if(isset($formDaten['colors'])) {
            $colors = $formDaten['colors'];
        }


        if ($searchTerm == NULL AND $category == NULL ) {
            return $this->render('OrthIndexBundle:Shop:shop.html.twig');
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');

        $em = $this->getDoctrine()->getManager();

        if ($request->query->get('p')) {
            $page = $request->query->get('p');
            $pageOffset = ($page - 1) * 12;
        }

        if($category !== NULL) {
        $categoryId = explode('-', $category);
        }
        if($childcategory !== NULL) {
        $categoryId = explode('-', $childcategory);
        }
        if($grandchildcategory !== NULL) {
        $categoryId = explode('-', $grandchildcategory);
        }

        $orderby = $request->query->get('orderby');
        if ($orderby == "null") {
            $orderby = NULL;
        }
        $categoryPath = NULL;
        if (isset($categoryId)) {
            $categoryPath = $em->getRepository('OrthIndexBundle:Categories')->getCategoryPath($categoryId[0]);
        } else {
            $categoryId[0] = NULL;
        }

        $finderVar = $this->container->get('fos_elastica.finder.search.variants');
        $finder = $this->container->get('fos_elastica.finder.search.articles');

        $elasticIndexArticles = $this->get('fos_elastica.index.search.articles');
        $elasticIndexVariants = $this->get('fos_elastica.index.search.variants');

        $results1 =  $em->getRepository('OrthIndexBundle:Articles')->getArticles($user, $searchTerm, $page, $pageOffset, $categoryId[0], $orderby, $colors, $finder, $elasticIndexArticles);
        $articles = $results1["articles"];
        $allArticles = $finder->find($results1['rQuery']->setFrom(0)->setSize(2000));
        $totalpages = ceil(count($allArticles)/12);
        $colorAttribute = $em->getRepository('OrthIndexBundle:Articles')->getColorAttributes($allArticles, $elasticIndexVariants, $colors);

        $results = $results1['aggs'];

        $articleCatgeories = $results->getAggregations()['catRef']['buckets'];
        $categoryTree = $em->getRepository('OrthIndexBundle:Articles')->buildCatTree($articleCatgeories);
        foreach ($articles as $article) {

            $article_refs[] = $article->getId();

          }
        //$bestImg = $em->getRepository('OrthIndexBundle:Articles')->getBestImg($searchTerm, $article_refs, $colors, $finderVar);
        //$bestImgIndex = 0;
        foreach ($articles as $article) {

            $article_refs[] = $article->getId();
            // returns actual price for each article e.g. normalprice, priceset price or customer price
            $price = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->getSingleCustomPrice($article, $user);
            // returns true or false ( true if there are price differences )
            $checkPriceDiff = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->checkPriceDifferences($article, $user);

            $article->setShowedPrice($price);
            $article->setPriceDiff($checkPriceDiff);
            $bestImg = $em->getRepository('OrthIndexBundle:Articles')->getBestImg($searchTerm, $article->getId(), $colors, $finderVar);

            if(isset($bestImg[0]) AND $bestImg[0]->getImages()->first()) {
                $article->setDisplayImgOnSearch($bestImg[0]->getImages()->first()->getPicName());
                $article->setDisplayImgId($bestImg[0]->getImages()->first()->getImgCrc32());
            } else {
                $article->setDisplayImgOnSearch($article->getImages()->first()->getPicName());
                $article->setDisplayImgId($article->getImages()->first()->getImgCrc32());
            }
            //$bestImgIndex++;
        }

        $colors = [];
        foreach ($colorAttribute as $color) {
          $colors[$color['key']] = $color['key'];
        }

        $formData = array();

        $form = $this->createFormBuilder($formData)
                    ->setMethod('get')
                    ->add('q', 'text', array('required' => false))
                    //->add('colors', 'choice', array('choices' => $colors, 'expanded' => true, 'multiple' => true, 'required' => false, 'choices_as_values' => true,
                    //'choice_attr' => array('schwarz' => array('data-color' => 'black'), 'blau' => array('data-color' => 'blue'), 'rot' => array('data-color' => '#FE2E2E'), 'grün' => array('data-color' => 'green'),
                    //'grau' => array('data-color' => 'grey'), 'weiss' => array('data-color' => 'white'), 'orange' => array('data-color' => 'orange'), 'gelb' => array('data-color' => 'yellow'), 'braun' => array('data-color' => 'brown'),
                    //'lila' => array('data-color' => 'purple'), 'pink' => array('data-color' => 'pink'), 'beige' => array('data-color' => 'beige'))))
                    ->getForm();

        $form->handleRequest($request);

        return $this->render('OrthIndexBundle:Shop:kategorien.html.twig', array('form' => $form->createView(), 'colorAttribute' => $colorAttribute, 'categoryPath' => $categoryPath, 'articles' => $articles, 'page' => $page, 'totalpages' => $totalpages, 'categories' => $categoryTree));

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

        $finder = $this->container->get('fos_elastica.finder.search.articles');
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

    public function shopAction() {

        return $this->render('OrthIndexBundle:Shop:shop.html.twig');
    }

}
