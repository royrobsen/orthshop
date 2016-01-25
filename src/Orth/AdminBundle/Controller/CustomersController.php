<?php

namespace Orth\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Orth\IndexBundle\Entity\Customers;

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
}
