<?php

namespace Orth\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orth\IndexBundle\Entity\Articles;
use Orth\IndexBundle\Entity\ShoppingCart;
use Orth\IndexBundle\Entity\ArticleImages;
use Orth\IndexBundle\Entity\Orders;
use Orth\IndexBundle\Entity\OrderPositions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Orth\IndexBundle\Entity\CustomersAddresses;
use Orth\IndexBundle\Form\Type\AddressType;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

class CheckoutController extends Controller
{
    public function checkoutAction(Request $request) {
        
        $cartSum = 0;
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');
        
        $addresses = new CustomersAddresses();      
        
        $em = $this->getDoctrine()->getManager();
        $addresses = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findBy(array('customerRef' => '1', 'primaryAddress' => '0', 'defaultDeliveryAddress' => '0'));
        $invoiceAddress = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findBy(array('customerRef' => '1', 'primaryAddress' => '1'));
                 
        $shoppingCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('userRef' => $user->getId()));
        
        foreach ($shoppingCart as $item) {
            
            $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('varRef' => $item->getVarRef(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()));
            
            if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                $price = $customerData->getCustomPrice();
            } else {
                $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($item->getVarRef());
                $price = $product->getPrice();
            }
            
            $cartSum = $cartSum + ($price * $item->getAmount());
        }
        
        $freight = 5.50;
        
        return $this->render('OrthIndexBundle:Checkout:checkout.html.twig', array('addresses' => $addresses, 'invoiceAddress' => $invoiceAddress, 'cartSum' => $cartSum, 'freight' => $freight));
    } 
    
    public function step2Action(Request $request) {
        
        $cartSum = 0;
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');      
        
        $invoiceId = $request->query->get('inv');
        
        // get invoice adress by submitted id
        $em = $this->getDoctrine()->getManager();
        $checkInvoiceId = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findOneBy(array('id' => $invoiceId, 'customerRef' => $user->getCustomerRef()));
        
        // check if invoiceAdress in get-Request match a user owned adress
        if($checkInvoiceId != NULL) {

        $addresses = new CustomersAddresses();      
        
        $em = $this->getDoctrine()->getManager();
        $addresses = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findBy(array('customerRef' => '1', 'primaryAddress' => '0', 'defaultDeliveryAddress' => '0'));
        $deliveryAddress = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findBy(array('customerRef' => '1', 'defaultDeliveryAddress' => '1'));
                 
        $shoppingCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('userRef' => $user->getId()));
        
        foreach ($shoppingCart as $item) {
            
            $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('varRef' => $item->getVarRef(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()));
            
            if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                $price = $customerData->getCustomPrice();
            } else {
                $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($item->getVarRef());
                $price = $product->getPrice();
            }
            
            $cartSum = $cartSum + ($price * $item->getAmount());
        }
        $freight = 5.50;
        
        return $this->render('OrthIndexBundle:Checkout:step2.html.twig', array('addresses' => $addresses, 'deliveryAddress' => $deliveryAddress, 'cartSum' => $cartSum, 'freight' => $freight));
        
        }
        // if id of invoiceAdress is not owned by users force exit!
        exit;
        
    }
    
    public function step3Action(Request $request) {
        
        $cartSum = 0;
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');      
        
        $invoiceId = $request->query->get('inv');
        $deliveryId = $request->query->get('del');
        
        // get invoice and delivery adress by submitted id's
        $em = $this->getDoctrine()->getManager();
        $checkInvoiceId = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findOneBy(array('id' => $invoiceId, 'customerRef' => $user->getCustomerRef()));
        $checkDeliveryId = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findOneBy(array('id' => $deliveryId, 'customerRef' => $user->getCustomerRef()));

        // check if invoiceAdress or delvieryAdress in get-Request match a user owned adress
        if($checkInvoiceId != NULL AND $checkDeliveryId != NULL ) {

        $addresses = new CustomersAddresses();      
        
        $em = $this->getDoctrine()->getManager();
        $addresses = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findBy(array('customerRef' => '1', 'primaryAddress' => '0', 'defaultDeliveryAddress' => '0'));
                 
        $shoppingCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('userRef' => $user->getId()));
        
        foreach ($shoppingCart as $item) {
            
            $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('varRef' => $item->getVarRef(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()));
            
            if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                $price = $customerData->getCustomPrice();
            } else {
                $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($item->getVarRef());
                $price = $product->getPrice();
            }
            
            $cartSum = $cartSum + ($price * $item->getAmount());
        }
        
        $freight = 5.50;
        
        return $this->render('OrthIndexBundle:Checkout:step3.html.twig', array('cartSum' => $cartSum, 'freight' => $freight));
        }
        exit;
        
    }
    
    public function step4Action(Request $request) {
        
        $cartSum = 0;
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');      
        
        $invoiceId = $request->query->get('inv');
        $deliveryId = $request->query->get('del');
        $deliveryKind = $request->query->get('delKind');
        
        // get invoice and delivery adress by submitted id's
        $em = $this->getDoctrine()->getManager();
        $checkInvoiceId = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findOneBy(array('id' => $invoiceId, 'customerRef' => $user->getCustomerRef()));
        $checkDeliveryId = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findOneBy(array('id' => $deliveryId, 'customerRef' => $user->getCustomerRef()));

        // check if invoiceAdress or delvieryAdress in get-Request match a user owned adress
        if($checkInvoiceId != NULL AND $checkDeliveryId != NULL AND  $deliveryKind != NULL) {
            
        $shoppingCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('userRef' => $user->getId()));
        
        foreach ($shoppingCart as $item) {
            
            $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('varRef' => $item->getVarRef(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()));
            
            if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                $price = $customerData->getCustomPrice();
            } else {
                $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($item->getVarRef());
                $price = $product->getPrice();
            }
            
            $cartSum = $cartSum + ($price * $item->getAmount());
        }
        
        $freight = 5.50;
        
            return $this->render('OrthIndexBundle:Checkout:step4.html.twig', array('cartSum' => $cartSum, 'freight' => $freight));
        }
        exit;
        
    }
    
    public function step5Action(Request $request) {
        
        $cartSum = 0;
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.authorization_checker');      
        
        $invoiceId = $request->query->get('inv');
        $deliveryId = $request->query->get('del');
        $deliveryKind = $request->query->get('delKind');
        $invoiceKind = $request->query->get('invKind');
        
        // get invoice and delivery adress by submitted id's
        $em = $this->getDoctrine()->getManager();
        $checkInvoiceId = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findOneBy(array('id' => $invoiceId, 'customerRef' => $user->getCustomerRef()));
        $checkDeliveryId = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findOneBy(array('id' => $deliveryId, 'customerRef' => $user->getCustomerRef()));

        // check if invoiceAdress or delvieryAdress in get-Request match a user owned adress
        if($checkInvoiceId != NULL AND $checkDeliveryId != NULL AND  $deliveryKind != NULL AND $invoiceKind != NULL) {
            
            $cart = NULL;
        if ($request->cookies->get('OrthCookie') != NULL ) {
            $cookieValue = $request->cookies->get('OrthCookie');
        
            
            $em = $this->getDoctrine()->getManager();  
            
            $user = $this->get('security.token_storage')->getToken()->getUser();
            
                    $securityContext = $this->container->get('security.authorization_checker');
                    
                    if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                        
                        $updateCarts = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('sessionId' => $cookieValue));
                        
                        if ($updateCarts != NULL) {
                            foreach ($updateCarts as $updateCart) {
                                $updateCart->setUserRef($user->getId());
                                $updateCart->setCustomerRef($user->getCustomerRef());

                                $em->persist($updateCart);
                                $em->flush();
                            }
                        }
                        
                        $cart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('userRef' => $user->getId()));
                        
                    } else {
                        $cart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('sessionId' => $cookieValue));
                    }
        }
            $cartItems = [];
            if ( $cart != NULL ) {

                foreach ($cart as $item) {
                    $em = $this->getDoctrine()->getManager();
                    $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($item->getVarRef());
                    //$varData = $em->getRepository('OrthIndexBundle:articleAttributeValues')->findBy(array('varRef' => $item->getVarRef())); 

                    $repository = $this->getDoctrine()->getRepository('OrthIndexBundle:articleAttributeValues');
                
                    $query = $repository->createQueryBuilder('aav')
                        ->select('aa, aav')
                        ->innerJoin('OrthIndexBundle:ArticleAttributes', 'aa', 'WITH', 'aav.attributeRef = aa.id')
                        ->where('aav.varRef = :string')
                        ->setParameter('string', $item->getVarRef())
                        ->getQuery();
                    $varData = $orders = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                    
                    $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('varRef' => $item->getVarRef(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()));
            
                    if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                        $price = $customerData->getCustomPrice();
                    } else {
                        $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($item->getVarRef());
                        $price = $product->getPrice();
                    }
                    
                    $cartItems[] = array('shortName' => $product->getArticles()->getShortName(), 'articlenumber' => $product->getSupplierArticleNumber(), 'amount' => $item->getAmount(), 'image' => $product->getArticles()->getImages(), 'varData' => $varData, 'price' => $price, 'varref' => $item->getVarRef());
                }
            }
            
        $shoppingCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('userRef' => $user->getId()));
        
        foreach ($shoppingCart as $item) {
            
            $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('varRef' => $item->getVarRef(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()));
            
            if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                $price = $customerData->getCustomPrice();
            } else {
                $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($item->getVarRef());
                $price = $product->getPrice();
            }
            
            $cartSum = $cartSum + ($price * $item->getAmount());
        }
        
        $freight = 5.50;

            return $this->render('OrthIndexBundle:Checkout:step5.html.twig', array('cart' => $cartItems, 'cartSum' => $cartSum, 'freight' => $freight));
            
       }
        exit;
        
    }
    
    public function cart2OrderAction(Request $request) {
        
        $cartItems = [];
        $freight = 5.50;
        
        $order = new Orders();
        
        $invoiceId = $request->query->get('inv');
        $deliveryId = $request->query->get('del');
        $deliveryKind = $request->query->get('delKind');
        $invoiceKind = $request->query->get('invKind');
        
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        
        $shoppingCart = $em->getRepository('OrthIndexBundle:ShoppingCart')->findBy(array('userRef' => $user->getId()));
        
        $order->setStatus(0);
        $order->setCustomerOrderNumber(0);
        $order->setInvoiceAdrRef($invoiceId);
        $order->setPaymentMethod($invoiceKind);
        $order->setShippingAdrRef($deliveryId);
        $order->setDeliveryMethod($deliveryKind);
        $order->setUserRef($user->getId());
        $order->setCustomerRef($user->getCustomerRef());
        $order->setCreatedDate(new \DateTime("now"));
        $em->persist($order);
        
        foreach ($shoppingCart as $item) {
            
            $orderPositions = new OrderPositions();
            $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->findOneBy(array('id' => $item->getVarRef()));

            $orderPositions->setOrders($order);
            $orderPositions->setPosAmount($item->getAmount());
            $orderPositions->setPosPrice($product->getPrice());
            $orderPositions->setVarRef($item->getVarRef());
            
            $repository = $this->getDoctrine()->getRepository('OrthIndexBundle:articleAttributeValues');
                
            $query = $repository->createQueryBuilder('aav')
                ->select('aa, aav')
                ->innerJoin('OrthIndexBundle:ArticleAttributes', 'aa', 'WITH', 'aav.attributeRef = aa.id')
                ->where('aav.varRef = :string')
                ->setParameter('string', $item->getVarRef())
                ->getQuery();
            $varData = $orders = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            
            $cartItems[] = array('shortName' => $product->getArticles()->getShortName(), 'articlenumber' => $product->getSupplierArticleNumber(), 'amount' => $item->getAmount(), 'image' => $product->getArticles()->getImages(), 'varData' => $varData, 'price' => $product->getPrice(), 'varref' => $item->getVarRef());

            $em->persist($orderPositions);
            $em->flush();
            
            $em->remove($item);
            $em->flush();
            
        }
        
 
        $em->flush();
        
        $message = \Swift_Message::newInstance()
        ->setSubject('Bestellbestätigung')
        ->setFrom('no-reply@ute-orth.de')
        ->setTo('r.brannath@ute-orth.de')
        ->setContentType("text/html")
        ->setBody(
            $this->renderView(
                'OrthIndexBundle:Mail:orderSuccessMail.html.twig',
                array('cartItems' => $cartItems, 'freight' => $freight),
            'text/html'
            )
        )
        ;
        $this->get('mailer')->send($message);
        
        return $this->render('OrthIndexBundle:Checkout:checkoutSuccess.html.twig');

    }
    
    
}
