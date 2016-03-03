<?php

namespace Orth\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orth\IndexBundle\Entity\Articles;
use Orth\IndexBundle\Entity\Searchindex;
use Symfony\Component\HttpFoundation\Request;
use Orth\IndexBundle\Form\Type\SearchType;
use Orth\IndexBundle\Classes\GermanStemmer;
use Orth\IndexBundle\Classes\ExtractCommonWords;

class IndexController extends Controller
{
    public function indexAction()
    {
        return $this->render('OrthIndexBundle:Index:index.html.twig');
        
    }
    public function mailSuccessAction()
    {
        return $this->render('OrthIndexBundle:Mail:orderSuccessMail.html.twig');
        
    }    public function searchAction(Request $request) {
        
        $products = new Products();
        
        $products = $this->getDoctrine()
            ->getRepository('OrthIndexBundle:Articles');
        
        if ( !$products ) {
            throw $this->createNotFoundException(
                'Keine Termine gefunden!');
            }
            
        
        if ($request->query->get('q')) {
            
           $string = "+" . str_replace("-", "*+", str_replace(" ", " +", $request->query->get('q')));
            
            $queryProducts = $products->createQueryBuilder('p')
                ->where('MATCH (p.beschreibung1, p.beschreibung2, p.beschreibung3, p.tags) AGAINST (:string BOOLEAN) > 0 GROUP BY p.mainNumber ORDER BY p.id ASC')
                ->setParameter('string', $string)   
                ->setMaxResults(40)
                ->getQuery();

            $products = $queryProducts->getResult();
                        $url = $this->get('router')->generate('orth_index_search', array('slug' => $string));

            }

        return $this->render('OrthIndexBundle:Index:results.html.twig', array('products' => $products));
        
    }
    
    public function articledetailAction($slug) {
        
        $product = new Products();
        
        $product = $this->getDoctrine()
            ->getRepository('OrthIndexBundle:Articles');
        
        if ( !$product ) {
            throw $this->createNotFoundException(
                'Keine Termine gefunden!');
            }
       
            $string = $slug;
            
            $queryProduct = $product->createQueryBuilder('p')
                ->where('p.mainNumber = :string')
                ->setParameter('string', $string)   
                ->setMaxResults(1)
                ->getQuery();

            $product = $queryProduct->getResult();
            
            return $this->render('OrthIndexBundle:Index:article_detail.html.twig', array('product' => $product));


            }
                
       
    #
    # Routing for general static sites
    #
            
    public function accountAction() {
        return $this->render('OrthIndexBundle:Index:account.html.twig');
    }
    
    
    public function impressumAction() {
        return $this->render('OrthIndexBundle:Index:impressum.html.twig');
    }
    
    public function agbAction() {
        return $this->render('OrthIndexBundle:Index:agb.html.twig');
    }
    
    public function datenschutzAction() {
        return $this->render('OrthIndexBundle:Index:datenschutz.html.twig');
    }
  
    public function widerrufAction() {
        return $this->render('OrthIndexBundle:Index:widerruf.html.twig');
    }
    
    public function arbeitsschutzAction() {
        return $this->render('OrthIndexBundle:Index:arbeitsschutz.html.twig');
    }
    
    public function berufsbekleidungAction() {
        return $this->render('OrthIndexBundle:Index:berufsbekleidung.html.twig');
    }
    
    public function emblemserviceAction() {
        return $this->render('OrthIndexBundle:Index:emblemservice.html.twig');
    }

    public function hygieneartikelAction() {
        return $this->render('OrthIndexBundle:Index:hygieneartikel.html.twig');
    }

    public function werkzeugtechnikAction() {
        return $this->render('OrthIndexBundle:Index:werkzeugtechnik.html.twig');
    } 
}