<?php

namespace Orth\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Orth\IndexBundle\Entity\Articles;
use Orth\IndexBundle\Entity\ArticleSuppliers;
use Orth\IndexBundle\Entity\ArticleAttributeValues;
use Orth\IndexBundle\Entity\ArticleImages;

use Orth\AdminBundle\Form\Type\ArticleType;
use Orth\AdminBundle\Form\Type\VariantsType;

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
        $images = $article->getImages()->getValues();
        
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

            $attachmentArray = $form['attachment']->getData();
            
            foreach ( $attachmentArray as $attachment ) {
                if ( $attachment != NULL ) {
                    $random = rand(0,99999999);
                    $dir = $this->container->getParameter('kernel.root_dir').'/../web/images/product/nw';
                    $extension = $attachment->getClientOriginalextension();
                    $attachment->move($dir, $random . '.' . $extension);

                    $image = new ArticleImages();

                    $image->setPicName($random.".".$extension);
                    $image->setArticles($article);
                    $image->setMimetype($extension);
                    $image->setSorting(0);

                    $em->persist($image);
                    $em->flush();
                }
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Artikel wurde erfolgreich gespeichert!');
            $this->container->get('fos_elastica.object_persister.search.article')->replaceOne($article);
            
        }
        return $this->render('OrthAdminBundle:Articles:article.html.twig', array('form' => $form->createView(), 'images' => $images, 'id' => $id, 'attrNames' => $usedattrNames, 'allAttrNames' => $allAttrNames));
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
    
    public function deleteArticleAction($id)
    {        
        
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('OrthIndexBundle:Articles')->findOneBy(array('id' => $id));
        $images = $em->getRepository('OrthIndexBundle:ArticleImages')->findBy(array('articles' => $article));
        $variants = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->findBy(array('articles' => $article));
        $variantsvalues = $em->getRepository('OrthIndexBundle:ArticleAttributeValues')->findBy(array('variants' => $variants));

        $em->remove($article);
        foreach ($variants as $variant) {
            $em->remove($variant);
            
            $variantsvalues = $em->getRepository('OrthIndexBundle:ArticleAttributeValues')->findBy(array('variants' => $variant));
            foreach ($variantsvalues as $val) {
                $em->remove($val);
            }
        }
        foreach ($images as $image) {
            $em->remove($image);
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Artikel wurde erfolgreich gelöscht!');
        
        return $this->redirect($this->generateUrl('orth_admin_articlelist'), 301);

    }
    
    public function deleteimgAction($artId, $imgId)
    {        
        
        $em = $this->getDoctrine()->getManager();
        $image = $em->getRepository('OrthIndexBundle:ArticleImages')->findOneBy(array('id' => $imgId));
        
        $em->remove($image);
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Bild wurde erfolgreich gelöscht!');
        
        return $this->redirect($this->generateUrl('orth_admin_article', array('id' => $artId)), 301);

    }
    
    public function newAction(Request $request)
    {
        
        $article = new Articles();

        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(new ArticleType(), $article);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $attrNames = $form['attrName']->getData();
            $attachmentArray = $form['attachment']->getData();
            $article->setModifiedDate(new \DateTime());
            $article->setDeliverable(1);
            $em->persist($article);
            
            foreach ( $attachmentArray as $attachment ) {
                if ( $attachment != NULL ) {
                    $random = rand(0,99999999);
                    $dir = $this->container->getParameter('kernel.root_dir').'/../web/images/product/nw';
                    $extension = $attachment->getClientOriginalextension();
                    $attachment->move($dir, $random . '.' . $extension);

                    $image = new ArticleImages();

                    $image->setPicName($random.".".$extension);
                    $image->setArticles($article);
                    $image->setMimetype($extension);
                    $image->setSorting(0);

                    $em->persist($image);
                    $em->flush();
                }
            }

            $em = $this->getDoctrine()->getManager();
            $request->request->set('attrNames', $attrNames);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Artikel wurde erfolgreich gespeichert!');
            $response = $this->forward('OrthAdminBundle:Articles:newarticlevar', array(
                'id'  => $article->getId(),
                'attrNames' => $attrNames,
            ));

            return $response;
       
        }
          
        return $this->render('OrthAdminBundle:Articles:new.html.twig', array('form' => $form->createView()));
    }
    
    public function newarticlevarAction(Request $request, $id = null, $attrNames = null)
    {

        $em = $this->getDoctrine()->getManager();
        $all = $request->request->all();
        $article = $em->getRepository('OrthIndexBundle:Articles')->findOneBy(array('id' => $id));
        
        if($request->request->get('data1') != NULL ) {
            $id = $request->request->get('artId');
            $article = $em->getRepository('OrthIndexBundle:Articles')->findOneBy(array('id' => $id));
            $attrNames = $request->request->get('attr');
            foreach ( $request->request->get('data1') as $formData) {
                
                $variant = new ArticleSuppliers();

                $variant->setArticles($article);
                $variant->setAmountUnit($request->request->get('amountunit'));
                $variant->setMinOrder($request->request->get('minorder'));
                $variant->setPriceUnit($request->request->get('priceunit'));
                $variant->setVpe($request->request->get('vpe'));
                $variant->setVpePackage($request->request->get('vpepackage'));
                $variant->setVpePalette($request->request->get('vpepalette'));
                $variant->setRank(0);
                $variant->setAttributes(0);
                $variant->setAddressRef(0);
                $variant->setSupplierArticleNumber($formData[0]);
                $variant->setPrice($formData[1]);
                
                $em->persist($variant);
                
                $i = 0;
                
                foreach ($formData['data2'] as $attrData) {
                    
                    if($i % 2 == 0) { 
                        $attrVal = new ArticleAttributeValues();

                        $attrVal->setVariants($variant);
                        $attrVal->setSorting(0);
                        $attrVal->setAttributeValue($attrData);
                        $attrName = $em->getRepository('OrthIndexBundle:ArticleAttributes')->findOneBy(array('id' => $attrNames[$i]));
                        $attrVal->setAttrName($attrName);
                        
                        //$attrVal->setAttributeRef($attrNames[$i]);
                    }
                    elseif($i % 2 != 0) {
                        $attrVal->setAttributeUnit($attrData);
                        $em->persist($attrVal);
                    }
                    
                    $i++;
                    
                }
                $em->flush();
                $this->container->get('fos_elastica.object_persister.search.article')->replaceOne($article);
            }
            return $this->redirect($this->generateUrl('orth_admin_article', array('id' => $id)), 301);
        }
         
        return $this->render('OrthAdminBundle:Articles:newVar.html.twig', array('attrNames' => $attrNames, 'artId' => $id, 'article' => $article));

    }
    
}
