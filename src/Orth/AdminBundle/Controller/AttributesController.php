<?php

namespace Orth\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Orth\IndexBundle\Entity\ArticleAttributes;

use Orth\AdminBundle\Form\Type\AttributeType;

class AttributesController extends Controller
{
    public function attributelistAction(Request $request)
    {
        
        return $this->render('OrthAdminBundle:Attributes:attributelist.html.twig');
        
    }

        public function jsonattributelistAction()
    {
        $request = $this->getRequest();
        
        $em = $this->getDoctrine()->getManager();
                    
        $query = $em->createQuery( "SELECT a FROM OrthIndexBundle:ArticleAttributes a");        
         
        $attributes = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                
        $data = array('data' => array());
        
        foreach ( $attributes as $attribute) {
                                  
            $data['data'][] = array($attribute['id'], $attribute['attributeName'], $attribute['id']);
            
        }   
            
        $response = new Response();
        $response->setContent(json_encode($data));
        
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
        
    }
    
    public function attributeAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
                 
        $attribute = new ArticleAttributes();
        
        $attribute = $em->getRepository('OrthIndexBundle:ArticleAttributes')->findOneBy(array('id' => $id));
                
        $form = $this->createForm(new AttributeType(), $attribute);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em->persist($attribute);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Das Attribute wurde erfolgreich gespeichert!');
            
        }
        
        return $this->render('OrthAdminBundle:Attributes:attribute.html.twig', array('form' => $form->createView(), 'attribute' => $attribute));
        
    }
    
    public function deleteAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $attribute = $em->getRepository('OrthIndexBundle:ArticleAttributes')->findOneBy(array('id' => $id));
        $attributeValues = $em->getRepository('OrthIndexBundle:ArticleAttributeValues')->findBy(array('attrName' => $attribute));
        
        if ( $attributeValues ) {
            $this->get('session')->getFlashBag()->add('danger', 'Dieses Attribut wird in Artikeln verwendet! Löschen nicht möglich!');
            
            return $this->redirectToRoute('orth_admin_attribute', array('id' => $id), 301);
            
        } else {

            $em->remove($attribute);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Das Attribut wurde erfolgreich gelöscht !');
            
        }
        
        return $this->redirect($this->generateUrl('orth_admin_attributelist'));
    }  
    
    public function newAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
                 
        $attribute = new ArticleAttributes();
                     
        $form = $this->createForm(new AttributeType(), $attribute);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em->persist($attribute);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Das Attribute wurde erfolgreich gespeichert!');
            
        }
        
        return $this->render('OrthAdminBundle:Attributes:new.html.twig', array('form' => $form->createView()));
        
    }
    
}
