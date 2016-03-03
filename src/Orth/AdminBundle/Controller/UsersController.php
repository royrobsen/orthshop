<?php

namespace Orth\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Orth\IndexBundle\Entity\Users;
use Orth\IndexBundle\Entity\Tokens;

use Orth\AdminBundle\Form\Type\UserType;

class UsersController extends Controller
{    
    
    public function userAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
                 
        $user = new Users();
        
        $user = $em->getRepository('OrthIndexBundle:Users')->findOneBy(array('id' => $id));
                    
        $form = $this->createForm(new UserType(), $user);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em->persist($user);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Der Benutzer wurde erfolgreich gespeichert!');
            
        }
        
        return $this->render('OrthAdminBundle:Users:user.html.twig', array('form' => $form->createView(), 'user' => $user));
        
    }
    
    public function newAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
                 
        $customer = $em->getRepository('OrthIndexBundle:Customers')->findOneBy(array('id' => $id));
        
        $user = new Users();
                
        $form = $this->createForm(new UserType(), $user);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $formData = $form->getData();
            
            function generateRandomString($length = 10) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                return $randomString;
            }
            $randomPassword = generateRandomString();
            $encoderFactory = $this->get('security.encoder_factory');
            $encoder = $encoderFactory->getEncoder($user);

            $salt = '$2a$12$uWepESKverBsrLAuOPY';

            $passkeyHash = $encoder->encodePassword($randomPassword, $salt);
            
            $user->setPassKey($passkeyHash);
            $user->setCustomer($customer);
            
            $token = new Tokens();
            
            $datetime = new \DateTime('tomorrow');
            $datetime->format('Y-m-d H:i:s');
            
            $token->setToken(md5(uniqid()));
            $token->setExpDate($datetime);
            $token->setUser($user);
                
            $em->persist($token);
            $em->persist($user);
            $em->flush();
            
            $message = \Swift_Message::newInstance()
                ->setSubject('Registrierung im OrthShop')
                ->setFrom('no-reply@ute-orth.de')
                ->setTo($formData->getEmail())
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView(
                        'OrthIndexBundle:Mail:adminregistrationMail.html.twig',
                        array('user' => $user, 'token' => $token, 'passkey' => $randomPassword),
                    'text/html'
                    )
                );
            
            $this->get('mailer')->send($message);
            
            $this->get('session')->getFlashBag()->add('success', 'Der Benutzer wurde erfolgreich gespeichert!');
            
            return $this->redirectToRoute('orth_admin_customer', array('id' => $customer->getId()), 301);
            
        }
        
        return $this->render('OrthAdminBundle:Users:new.html.twig', array('form' => $form->createView()));
        
    }
    
    public function deactivateAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
                 
                
        $user = new Users();
                
        $user = $em->getRepository('OrthIndexBundle:Users')->findOneBy(array('id' => $id));
        $customerId = $user->getCustomerRef();
        
        $user->setActive(0);
        
        $em->persist($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Der Benutzer wurde erfolgreich deaktiviert!');

        return $this->redirectToRoute('orth_admin_customer', array('id' => $customerId), 301);
                    
    }
    
    public function activateAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
                 
                
        $user = new Users();
                
        $user = $em->getRepository('OrthIndexBundle:Users')->findOneBy(array('id' => $id));
        $customerId = $user->getCustomerRef();
        
        $user->setActive(1);
        
        $em->persist($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Der Benutzer wurde erfolgreich aktiviert!');

        return $this->redirectToRoute('orth_admin_customer', array('id' => $customerId), 301);
                    
    }
       
}
