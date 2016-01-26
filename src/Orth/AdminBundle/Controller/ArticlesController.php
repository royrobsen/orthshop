<?php

namespace Orth\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Orth\IndexBundle\Entity\Articles;
use Orth\IndexBundle\Entity\ArticleSuppliers;
use Orth\IndexBundle\Entity\ArticleAttributeValues;

use Orth\AdminBundle\Form\Type\ArticleType;

class ArticlesController extends Controller
{
    public function articlelistAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
         
        $searchTerm = $request->query->get('q');

        $page = 0;
        $pageOffset = 0;
        
        if ($request->query->get('p')) {
            $page = $request->query->get('p');
            $pageOffset = ($page - 1) * 12;
        }
        
        $finder = $this->container->get('fos_elastica.finder.search.article');
        $boolQuery = new \Elastica\Query\BoolQuery();
        $articles = "";  
        $totalpages = 0;
        if($request->query->get('q') != NULL )  {  
            $searchTerm = $request->query->get('q');
         
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
        } 
        return $this->render('OrthAdminBundle:Articles:articlelist.html.twig', array('totalpages' => $totalpages, 'articles' => $articles));
    }

    public function articleAction($id, Request $request)
    {
        
        $article = new Articles();
        $variants = new ArticleSuppliers();
        
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('OrthIndexBundle:Articles')->find($id);
        $variants = $article->getVariants()->getValues();
        foreach ($variants as $variant) {
              $variantvalues = $variant->getVariantvalues()->getValues();
              foreach ($variantvalues as $variantvalue) {
                  $attrName = $em->getRepository('OrthIndexBundle:ArticleAttributes')->find($variantvalue->getAttributeRef());
                  $attributeRef[$variantvalue->getAttributeRef()] = $attrName->getAttributeName();
              }
        }
        foreach ( $attributeRef as $key=>$value ) {
            $usedattrNames[] = array('id' => $key, 'attributeName' => $value);
        }
        $allAttrNames = $em->getRepository('OrthIndexBundle:ArticleAttributes')->findAll();

        $form = $this->createForm(new ArticleType(), $article);
        
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Artikel wurde erfolgreich gespeichert!');
            
        }
        return $this->render('OrthAdminBundle:Articles:article.html.twig', array('form' => $form->createView(), 'attrNames' => $usedattrNames, 'allAttrNames' => $allAttrNames));
    }
    
    public function fixattrAction($id)
    {

        $article = new Articles();
        $variants = new ArticleSuppliers();
        
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('OrthIndexBundle:Articles')->find($id);
        
        $variants = $article->getVariants()->getValues();
        
        foreach ($variants as $variant) {
              $variantvalues = $variant->getVariantvalues()->getValues();
              foreach ($variantvalues as $variantvalue) {
                  $attrName = $em->getRepository('OrthIndexBundle:ArticleAttributes')->find($variantvalue->getAttributeRef());
                  $attributeRef[$variantvalue->getAttributeRef()] = $attrName->getAttributeName();
              }
        }
        
        foreach ( $attributeRef as $key=>$value ) {
            $attrNames[] = array('id' => $key, 'attributeName' => $value);
            $attrName1[] = $key;
        }

        foreach ($variants as $variant) {
            $variantvalues = $variant->getVariantvalues()->getValues();
            $attributes= [];
            foreach ($variantvalues as $variantvalue) {
                  $attrName = $em->getRepository('OrthIndexBundle:ArticleAttributes')->find($variantvalue->getAttributeRef());
                  $attributes[] = $variantvalue->getAttributeRef();     
            }
            $arrayDiff = array_diff($attrName1, $attributes);
            if($arrayDiff != NULL) {
                foreach ($arrayDiff as $singleDiff) {
                    $variantvalue = new ArticleAttributeValues();
                    $attrName = $em->getRepository('OrthIndexBundle:ArticleAttributes')->find($singleDiff);
                    $varRef = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($variant->getId());
                    $variantvalue->setAttrName($attrName);
                    $variantvalue->setAttributeValue("");
                    $variantvalue->setAttributeUnit("");
                    $variantvalue->setSorting(0);
                    $variantvalue->setVariants($varRef);
                    $em->persist($variantvalue);
                    $em->flush();
                }
            }
        }
        
    return $this->redirect($this->generateUrl('orth_admin_article', array('id' => $id)), 301);
        
    }
    
    public function deleteattrAction($artId, $attrId)
    {        
        
        $em = $this->getDoctrine()->getManager();
        $variants = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->findBy(array('articleRef' => $artId));
        
        foreach ($variants as $variant) {
            $variantvalue = $em->getRepository('OrthIndexBundle:ArticleAttributeValues')->findOneBy(array('varRef' => $variant->getId(), 'attributeRef' => $attrId));
            $em->remove($variantvalue);
            $em->flush();
        }

        $this->get('session')->getFlashBag()->add('notice', 'Attribut wurde erfolgreich gelöscht!');
        
        return $this->redirect($this->generateUrl('orth_admin_article', array('id' => $artId)), 301);

    }
    
    public function addattrAction($artId, Request $request)
    {        
        
        $attrId = $request->query->get('attrId');
        
        $em = $this->getDoctrine()->getManager();
        $variants = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->findBy(array('articleRef' => $artId));
        
        foreach ($variants as $variant) {

            $variantvalue = new ArticleAttributeValues();
            $attrName = $em->getRepository('OrthIndexBundle:ArticleAttributes')->find($attrId);
            $varRef = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($variant->getId());
            $variantvalue->setAttrName($attrName);
            $variantvalue->setAttributeValue("");
            $variantvalue->setAttributeUnit("");
            $variantvalue->setSorting(0);
            $variantvalue->setVariants($varRef);
            $em->persist($variantvalue);
            $em->flush();
        }

        $this->get('session')->getFlashBag()->add('notice', 'Attribut wurde erfolgreich gelöscht!');
        
        return $this->redirect($this->generateUrl('orth_admin_article', array('id' => $artId)), 301);

    }
}
