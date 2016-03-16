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

class OciController extends Controller
{
    
    public function searchAction(Request $request) {
        
        $em = $this->getDoctrine()->getManager();
         
        
        $username = $request->query->get('oci_username');
        $password = $request->query->get('oci_password');
        $hookurl = $request->query->get('hookurl');
        
        $query = $em->createQuery("SELECT u FROM Orth\IndexBundle\Entity\Users u WHERE u.email = :username");
        $query->setParameter('username', $username);
        $user = $query->getOneOrNullResult();
        
        if ($user) {
          // Get the encoder for the users password
          $encoder_service = $this->get('security.encoder_factory');
          $encoder = $encoder_service->getEncoder($user);

          // Note the difference
          if ($encoder->isPasswordValid($user->getPassword(), $password, '$2a$12$uWepESKverBsrLAuOPY')) {
            // Get profile list
          } else {
              dump('fail1');
            exit;
          }
        } else {
            dump('fail');
            exit;
        }
        
        $searchTerm = $request->query->get('SEARCHSTRING');

        $page = 0;
        $pageOffset = 0;
        
        if ($request->query->get('p')) {
            $page = $request->query->get('p');
            $pageOffset = ($page - 1) * 12;
        }
        
        $catId = $request->query->get('cid');

        $finder = $this->container->get('fos_elastica.finder.search.article');
        $boolQuery = new \Elastica\Query\BoolQuery();
                
        if ($request->query->get('c') != NULL AND $request->query->get('SEARCHSTRING') == NULL ) {
            $catid = $request->query->get('c');
            $categoryArray = [$request->query->get('c')];
            $rootCategories = $em->getRepository('OrthIndexBundle:Categories')->findBy(array('parentId' => $catid));
            
            foreach ($rootCategories as $childCategory ) {
                $childCategories = $em->getRepository('OrthIndexBundle:Categories')->findBy(array('parentId' => $childCategory->getId()));
                $categoryArray[] = $childCategory->getId();
                foreach ($childCategories as $grandchildCategory ) {
                    $categoryArray[] = $grandchildCategory->getId();
                }
            }
   
            $categoryQuery = new \Elastica\Query\Terms();
            $categoryQuery->setTerms('catRef', $categoryArray);
            $boolQuery->addMust($categoryQuery);
            
        } elseif ($request->query->get('c') != NULL ) {
            $catid = $request->query->get('c');
            
            $categoryQuery = new \Elastica\Query\Terms();
            $categoryQuery->setTerms('catRef', array($catid));
            $boolQuery->addMust($categoryQuery);
        }
        
        if($request->query->get('SEARCHSTRING')) {

            $fieldQuery = new \Elastica\Query\Match();
            $fieldQuery->setFieldQuery('allField', $searchTerm);
            $fieldQuery->setFieldOperator('allField', 'AND');
            $fieldQuery->setFieldMinimumShouldMatch('allField', '80%');
            $fieldQuery->setFieldAnalyzer('allField', 'custom_search_analyzer');
            $boolQuery->addMust($fieldQuery);
        } 
        
        $query = new \Elastica\Query();
        $query->setQuery($boolQuery);
        $totalpages = ceil(count($finder->find($query))/12);
        $query->setSize(100);
        $query->setFrom($pageOffset);        
        $articles = $finder->find($query);
        foreach ($articles as $article) {
            $variants = $article->getVariants();
            foreach ($variants as $variant) {

                $price = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->getCustomPrice(array('id' => $variant), $user);
                $category = $em->getRepository('OrthIndexBundle:Categories')->getRootCategory($article->getCategory()->getId());
                $attribute = "";
                foreach ($variant->getVariantvalues() as $values) {
                    $attribute .= " " . $values->getAttrname()->getAttributeName() . " " . $values->getAttributeValue() . "" . $values->getAttributeUnit();
                }
                if($variant->getVariantvalues()[0]){
                    dump($article->getImages());
                $result[] = array('shortName' => $article->getShortName() . "" . $attribute, 'articleNumber' => $variant->getSupplierArticleNumber(), 'price' => $price, 'category' => $category, 'image' => $article->getImages()[0]->getPicName());
                }
            }
        }

        return $this->render('OrthIndexBundle:Oci:ocioutput.html.twig', array('results' => $result, 'page' => $page, 'totalpages' => $totalpages, 'hookurl' => $hookurl));
        
     }   
    
     public function ocipunchoutAction() {
         
        $user = $this->get('security.token_storage')->getToken()->getUser(); 
         
        if ($request->cookies->get('OrthCookie') != NULL ) {
            
            $cookieValue = $request->cookies->get('OrthCookie');
            $cart = $shoppingCart->getCartItems($user, $cookieValue);
            
        }
         
        dump($cart);
        exit;
        foreach ($articles as $article) {
            $variants = $article->getVariants();
            foreach ($variants as $variant) {

                $price = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->getCustomPrice(array('id' => $variant), $user);
                $category = $em->getRepository('OrthIndexBundle:Categories')->getRootCategory($article->getCategory()->getId());
                $attribute = "";
                foreach ($variant->getVariantvalues() as $values) {
                    $attribute .= " " . $values->getAttrname()->getAttributeName() . " " . $values->getAttributeValue() . "" . $values->getAttributeUnit();
                }
                if($variant->getVariantvalues()[0]){
                $result[] = array('shortName' => $article->getShortName() . "" . $attribute, 'articleNumber' => $variant->getSupplierArticleNumber(), 'price' => $price, 'category' => $category);
                }
            }
        }
        
        return $this->render('OrthIndexBundle:Oci:ocioutput.html.twig', array('results' => $result, 'page' => $page, 'totalpages' => $totalpages, 'hookurl' => $hookurl));

     }
     
}
