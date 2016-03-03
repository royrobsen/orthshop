<?php

namespace Orth\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Orth\IndexBundle\Entity\Customers;

use Orth\AdminBundle\Form\Type\CustomerType;

class CustomersController extends Controller
{
    public function customerlistAction()
    {
        return $this->render('OrthAdminBundle:Customers:customerlist.html.twig');
    }
    
        public function jsoncustomerlistAction()
    {
        $request = $this->getRequest();
        
        $em = $this->getDoctrine()->getManager();
                    
        $query = $em->createQuery( "SELECT c FROM OrthIndexBundle:Customers c");        
         
        $customers = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                
        $data = array('data' => array());
        
        foreach ( $customers as $customer) {
                                  
            $data['data'][] = array($customer['id'], $customer['lastName'], $customer['firstName'], $customer['companyName1'], $customer['companyName2'],$customer['companyName3'], $customer['id']);
            
        }   
            
        $response = new Response();
        $response->setContent(json_encode($data));
        
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
        
    }
    
    public function customerAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
                 
        $customer = new Customers();
        
        $customer = $em->getRepository('OrthIndexBundle:Customers')->findOneBy(array('id' => $id));
            
        $users = $customer->getUser();
        
        $form = $this->createForm(new CustomerType(), $customer);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em->persist($customer);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Der Kunde wurde erfolgreich gespeichert!');
            
        }
        
        return $this->render('OrthAdminBundle:Customers:customer.html.twig', array('form' => $form->createView(), 'customer' => $customer, 'users' => $users));
        
    }
    
    public function newAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
                 
        $customer = new Customers();
                
        $form = $this->createForm(new CustomerType(), $customer);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em->persist($customer);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Der Kunde wurde erfolgreich gespeichert!');
            
            return $this->redirectToRoute('orth_admin_newuser', array('id' => $customer->getId()), 301);
            
        }
        
        return $this->render('OrthAdminBundle:Customers:new.html.twig', array('form' => $form->createView()));
        
    }
    
}
