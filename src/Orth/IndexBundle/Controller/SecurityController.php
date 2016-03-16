<?php

namespace Orth\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Orth\IndexBundle\Entity\Users;
use Orth\IndexBundle\Entity\Tokens;


class SecurityController extends Controller
{
    
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        
        $lastUsername = $authenticationUtils->getLastUsername();
        
        $error = $authenticationUtils->getLastAuthenticationError();
        
        $response = new Response();
         
        return $this->render(
            'OrthIndexBundle:Index:account.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error' => $error,
            )  
        );
    }
        
    public function loginCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }    
    
    public function passwortvergessenAction(Request $request)
    {

        $securityContext = $this->container->get('security.authorization_checker');
        
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            
            $mail = new Users();
            
            $form = $this->createFormBuilder($mail)
                    ->add('email', 'email')
                    ->add('save', 'submit', array('label' => 'Abschicken'))
                    ->getForm();
            
            $form->handleRequest($request);

            if ($form->isValid()) {
                
                $formData = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('OrthIndexBundle:Users')->findOneBy(array('email' => $formData->getEmail()));
                
                if ($user) {
                    
                    $datetime = new \DateTime('tomorrow');
                    $datetime->format('Y-m-d H:i:s');

                    $token = new Tokens();

                    $token->setToken(md5(uniqid()));
                    $token->setExpDate($datetime);
                    $token->setUser($user);

                    $em->persist($token);
                    $em->flush();
                
                    $message = \Swift_Message::newInstance()
                    ->setSubject('Passwort zurücksetzen')
                    ->setFrom('no-reply@ute-orth.de')
                    ->setTo($formData->getEmail())
                    ->setContentType("text/html")
                    ->setBody(
                        $this->renderView(
                            'OrthIndexBundle:Mail:passwortreset.html.twig',
                            array('user' => $user, 'token' => $token),
                        'text/html'
                        )
                    );
                    
                    $this->get('mailer')->send($message);
                    
                    $this->get('session')->getFlashBag()->add('notice', 'Wir haben Ihnen eine E-Mail zugeschickt! Bitte folgen Sie dem Link in der E-Mail, um Ihr Passwort zurückzusetzen!');
                    
                } else {
                    
                    $this->get('session')->getFlashBag()->add('alert', 'Die E-Mailadresse wurde nicht gefunden! Bitte erstellen Sie sich ein neues Konto!');

                }
                               
            }
            
            return $this->render('OrthIndexBundle:Account:passwortvergessen.html.twig', array('form' => $form->createView()));
            
        } else {
            exit;
        }
    }    
        
    public function passwortresetAction(Request $request, $token) {
            
        $em = $this->getDoctrine()->getManager();
        
        $checkToken = $em->getRepository('OrthIndexBundle:Tokens')->findOneBy(array('token' => $token));
        
        if( $checkToken != NULL AND $checkToken->getExpDate() > new \DateTime()) {
        
            $user = new Users(); 
            
            $user = $em->getRepository('OrthIndexBundle:Users')->findOneBy(array('id' => $checkToken->getUserRef()));
            $user->setActive(true);
            
            $form = $this->createFormBuilder($user)
                ->add('newPassword', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Passwörter stimmen nicht überein',
                    'required' => true,
                    'first_options'  => array('label' => 'Neues Passwort'),
                    'second_options' => array('label' => 'Wiederholen'),
                        ))
                ->add('save', 'submit', array('label' => 'Abschicken'))
                ->getForm();
            
            $form->handleRequest($request);

            if ($form->isValid()) {
                $formData = $form->getData();
                $encoderFactory = $this->get('security.encoder_factory');
                $encoder = $encoderFactory->getEncoder($user);

                $salt = '$2a$12$uWepESKverBsrLAuOPY';

                $passkeyHash = $encoder->encodePassword($formData->getNewPassword(), $salt);
                $user->setPasskey($passkeyHash);
                
                $em->remove($checkToken);
                $em->persist($user);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'Passwort erfolgreich zurückgesetzt! Sie können sich nun anmelden.');
        
                return $this->redirect($this->generateUrl('orth_index_account'));
                
            }
            return $this->render('OrthIndexBundle:Account:passwortreset.html.twig', array('form' => $form->createView()));
        } else {
            
            $this->get('session')->getFlashBag()->add('alert', 'Link nicht mehr gültig! Bitte fordern Sie ein neues Passwort an!');

        }
        
        return $this->redirect($this->generateUrl('orth_index_account'));
    }
          
    
}