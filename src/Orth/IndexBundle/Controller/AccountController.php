<?php

namespace Orth\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Orth\IndexBundle\Entity\CustomersAddresses;
use Orth\IndexBundle\Entity\Customcategory;
use Orth\IndexBundle\Entity\Users;
use Orth\IndexBundle\Entity\UserPermissions;
use Orth\IndexBundle\Form\Type\AddressType;
use Orth\IndexBundle\Form\Type\UserType;
use Orth\IndexBundle\Form\Type\PasswordType;
use Orth\IndexBundle\Form\Type\CustomcategoryType;
use Orth\IndexBundle\Form\Model\ChangePassword;

class AccountController extends Controller
{
    public function myaccountAction() {
        return $this->render('OrthIndexBundle:Account:myaccount.html.twig');
    } 
    public function orderhistoryAction() {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();
        
        $query = $em->createQuery( "SELECT o, sum(op.posPrice * op.posAmount) as sumvalue FROM OrthIndexBundle:Orders o JOIN o.positions op WHERE o.customerRef = :customerRef GROUP BY o.id")
                ->setParameter(':customerRef', $user->getCustomerRef());
        
        $orders = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $this->render('OrthIndexBundle:Account:orderhistory.html.twig', array('orders' => $orders));
    }  
    public function orderAction($id) {
        
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
        
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery( "SELECT o,op FROM OrthIndexBundle:Orders o JOIN o.positions op WHERE o.customerRef = :customerRef AND o.id = :id")
                    ->setParameter(':customerRef', $user->getCustomerRef())
                    ->setParameter(':id', $id);

            $order = $query->getResult();

            $invoiceAddress = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findBy(array('customerRef' => $user->getCustomerRef(), 'id' => $order[0]->getInvoiceAdrRef())); 
            $shippingAddress = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findBy(array('customerRef' => $user->getCustomerRef(), 'id' => $order[0]->getShippingAdrRef())); 

                foreach ($order[0]->getPositions() as $item) {

                    $em = $this->getDoctrine()->getManager();
                    $product = $em->getRepository('OrthIndexBundle:ArticleSuppliers')->find($item->getVarRef());

                    $repository = $this->getDoctrine()->getRepository('OrthIndexBundle:articleAttributeValues');

                    $query = $repository->createQueryBuilder('aav')
                        ->select('aa, aav')
                        ->innerJoin('OrthIndexBundle:ArticleAttributes', 'aa', 'WITH', 'aav.attributeRef = aa.id')
                        ->where('aav.varRef = :string')
                        ->setParameter('string', $item->getVarRef())
                        ->getQuery();

                    $varData = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                    $price = $product->getPrice();

                if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                    $customerData = $em->getRepository('OrthIndexBundle:Customerdata')->findOneBy(array('varRef' => $item->getVarRef(), 'userRef' => $user->getId(), 'customerRef' => $user->getCustomerRef()));
                    if($customerData != NULL AND $customerData->getCustomPrice() != 0) {
                        $price = $customerData->getCustomPrice();
                    }
                }  

                    $cartItems[] = array('shortName' => $product->getArticles()->getShortName(), 'artId' => $product->getArticles()->getId(), 'articlenumber' => $product->getSupplierArticleNumber(), 'amount' => $item->getPosAmount(), 'image' => $product->getArticles()->getImages(), 'varData' => $varData, 'price' => $price, 'varref' => $item->getVarRef());
                }
        
        } else { exit; }
        return $this->render('OrthIndexBundle:Account:order.html.twig', array('order' => $order, 'invAdr' => $invoiceAddress, 'delAdr' => $shippingAddress, 'cart' => $cartItems));
    }  
    public function configAction() {
        return $this->render('OrthIndexBundle:Account:config.html.twig');
    }  
    public function myaddressAction() {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $addresses = new CustomersAddresses();      
        
        $em = $this->getDoctrine()->getManager();
        $addresses = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findBy(array('customerRef' => $user->getCustomerRef(), 'primaryAddress' => '0', 'defaultDeliveryAddress' => '0'));
        $invoiceAddress = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findBy(array('customerRef' => $user->getCustomerRef(), 'primaryAddress' => '1'));
        $deliveryAddress = $em->getRepository('OrthIndexBundle:CustomersAddresses')->findBy(array('customerRef' => $user->getCustomerRef(), 'defaultDeliveryAddress' => '1'));
                  
        return $this->render('OrthIndexBundle:Account:myaddress.html.twig', array('addresses' => $addresses, 'invoiceAddress' => $invoiceAddress, 'deliveryAddress' => $deliveryAddress));
    } 
    public function addaddressAction(Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $addresses = new CustomersAddresses(); 

        $form = $this->createForm(new AddressType(), $addresses);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $addresses->setCustomerRef($user->getCustomerRef());
            $addresses->setPrimaryAddress(0);
            $addresses->setDefaultDeliveryAddress(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($addresses);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Adresse wurde erfolgreich hinzugefügt!');
            
            return $this->redirectToRoute('orth_account_myaddress');
        }
        
        return $this->render('OrthIndexBundle:Account:addaddress.html.twig', array('form' => $form->createView()));
    } 
    public function deleteAddressAction($id) {
          
        $em = $this->getDoctrine()->getManager();
        $address = $em->getRepository('OrthIndexBundle:CustomersAddresses')->find($id);
        
        $em->remove($address);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', 'Adresse wurde erfolgreich gelöscht!');
        
        return $this->redirectToRoute('orth_account_myaddress');
    } 
    public function editaddressAction($id, Request $request) {
        
        $address = new CustomersAddresses(); 
        
        $em = $this->getDoctrine()->getManager();
        $address = $em->getRepository('OrthIndexBundle:CustomersAddresses')->find($id);
        
        $form = $this->createForm(new AddressType(), $address);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Adresse wurde erfolgreich gespeichert!');
            
            return $this->redirectToRoute('orth_account_myaddress');
        }
        
        return $this->render('OrthIndexBundle:Account:editaddress.html.twig', array('form' => $form->createView()));
    } 
    public function personalinfoAction(Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
                
        $form = $this->createForm(new UserType(), $user);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            
            
            $em->persist($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Ihre Daten wurden erfolgreich gespeichert!');
        }
        
        return $this->render('OrthIndexBundle:Account:personalinfo.html.twig', array('form' => $form->createView()));
    } 
    public function wishlistAction() {
        return $this->render('OrthIndexBundle:Account:wishlist.html.twig');
    } 
    public function changePasswordAction(Request $request) {
        
      $changePasswordModel = new ChangePassword();
      
      $form = $this->createForm(new PasswordType(), $changePasswordModel);
      
      $authenticationUtils = $this->get('security.authentication_utils');
      
      $form->handleRequest($request);

      if ($form->isValid()) {
          
          $user = $this->get('security.token_storage')->getToken()->getUser();
          
          $encoderFactory = $this->get('security.encoder_factory');
          $encoder = $encoderFactory->getEncoder($user);
          
          $salt = '$2a$12$uWepESKverBsrLAuOPY';
          $newPw = $form->getData();
          dump($newPw);
          $password = $encoder->encodePassword($newPw->getNewPassword(), $salt);
          $user->setPasskey($password);
          
          $em = $this->getDoctrine()->getManager();
          $em->persist($user);
          $em->flush();
          
          $this->get('session')->getFlashBag()->add('success', 'Ihr Passwort wurde erfolgreich geändert. Sie können sich nun damit einloggen!');

          
      }
        $error = "";
          return $this->render('OrthIndexBundle:Account:changePw.html.twig', array(
          'form' => $form->createView(), 'error' => $error));
      }
      
    public function configCategoriesAction() {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $categories = new Customcategory();      
        
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef(), 'userRef' => $user->getId(), 'parentRef' => NULL));
             
        return $this->render('OrthIndexBundle:Account:categories.html.twig', array('categories' => $categories));
    }  
      
    public function addcategoryAction(Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $category = new Customcategory(); 
        $userPerm = new UserPermissions(); 
        
        $form = $this->createForm(new CustomcategoryType(), $category);
        $form->remove('parentRef');
        $form->remove('userRef');
        $form->remove('customerRef');
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
                       
            $category->setCustomerRef($user->getCustomerRef());
            $category->setUserRef($user->getId());
            $category->setParentRef(NULL);
                        
            $userPerm->setUserRef($user->getId());
            $userPerm->setCustcat($category);
            $userPerm->setPermStatus(1);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->persist($userPerm);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('notice', 'Kategorie wurde erfolgreich hinzugefügt!');
            
            return $this->redirectToRoute('orth_account_config_categories');
        }
        
        return $this->render('OrthIndexBundle:Account:addcategory.html.twig', array('form' => $form->createView()));
    } 
    
    public function addsubcategoryAction(Request $request, $id) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $category = new Customcategory(); 
        $userPerm = new UserPermissions(); 
        
        $form = $this->createForm(new CustomcategoryType(), $category);
        $form->remove('parentRef');
        $form->remove('userRef');
        $form->remove('customerRef');
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            
            $category->setCustomerRef($user->getCustomerRef());
            $category->setUserRef($user->getId());
            $category->setParentRef($id);
            
            $userPerm->setUserRef($user->getId());
            $userPerm->setCustcat($category);
            $userPerm->setPermStatus(1);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->persist($userPerm);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Kategorie wurde erfolgreich hinzugefügt!');
            
            return $this->redirectToRoute('orth_account_config_categories');
        }
        
        return $this->render('OrthIndexBundle:Account:addcategory.html.twig', array('form' => $form->createView()));
    } 
        
    public function categoryAction($id) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $categories = new Customcategory();      
        
        $em = $this->getDoctrine()->getManager();
        
        $mainCategory = $em->getRepository('OrthIndexBundle:Customcategory')->findOneBy(array('customerRef' => $user->getCustomerRef(), 'id' => $id));
               
        $categories = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef(), 'parentRef' => $id));
            
        if($mainCategory->getParentRef() == NULL ) {
            $level = 0;
        } else {
            $parentCat = $em->getRepository('OrthIndexBundle:Customcategory')->findOneBy(array('customerRef' => $user->getCustomerRef(), 'id' => $mainCategory->getParentRef()));

            if ($parentCat != NULL AND $parentCat->getParentRef() == NULL) {
                $level = 1;
            } 
        }
        return $this->render('OrthIndexBundle:Account:category.html.twig', array('categories' => $categories, 'mainCategory' => $mainCategory, 'level' => $level));
    }  
    
    public function subcategoryAction($id) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $categories = new Customcategory();      
        
        $em = $this->getDoctrine()->getManager();
        
        $mainCategory = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef(), 'id' => $id));
                
        $categories = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef(), 'parentRef' => $id));
              
        return $this->render('OrthIndexBundle:Account:subcategory.html.twig', array('categories' => $categories, 'mainCategory' => $mainCategory));
    }  
    
    public function myuserAction() {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $users = new Users();      
        
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('OrthIndexBundle:Users')->findBy(array('customerRef' => $user->getCustomerRef()));
             
        return $this->render('OrthIndexBundle:Account:myusers.html.twig', array('users' => $users, 'loggedInUser' => $user));
    }  
    
    public function myusereditAction($id, Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $users = new Users();      
        
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('OrthIndexBundle:Users')->findOneBy(array('customerRef' => $user->getCustomerRef(), 'id' => $id));
                             
        $form = $this->createForm(new UserType(), $users);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($users);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('notice', 'Ihre Daten wurden erfolgreich gespeichert!');
        }
        
        return $this->render('OrthIndexBundle:Account:edituser.html.twig', array('form' => $form->createView(), 'users' => $users));
        
    }  
    
    public function myusermanagerAction($id, Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $users = new Users();      
        
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('OrthIndexBundle:Users')->findOneBy(array('customerRef' => $user->getCustomerRef(), 'id' => $id));
                                            
        if ( $request->request->all()) {

            $formPost = $request->request->all();
  
            foreach ($formPost as $key => $value) {
                
                   $userPerm = new UserPermissions();  
                   $userPerm = $em->getRepository('OrthIndexBundle:UserPermissions')->findOneBy(array('userRef' => $id, 'custcatRef' => $key));

                   $custCat= $em->getRepository('OrthIndexBundle:Customcategory')->findOneBy(array('id' => $key));
    
                if ( $userPerm == NULL ) {
                    
                    $userPerm2 = new UserPermissions(); 
                    
                    $userPerm2->setCustcat($custCat);
                    $userPerm2->setUserRef($id);
                    $userPerm2->setPermStatus(1);
                    
                    $em->persist($userPerm2);
                    $em->flush();
                    
                } else {
                    
                    $userPerm->setCustcat($custCat);
                    $userPerm->setUserRef($id);
                    $userPerm->setPermStatus($value);

                    $em->persist($userPerm);
                    $em->flush();

                    $articleCat = $userPerm->getCustcat();
                    $articleCats1 = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('parentRef' => $articleCat->getId()));
                    
                    foreach ($articleCats1 as $articleCat) {
           
                        $userPerm3 = $em->getRepository('OrthIndexBundle:UserPermissions')->findOneBy(array('userRef' => $id, 'custcatRef' => $articleCat->getId()));
                        if ( $userPerm3 == NULL ) {

                            $userPerm3 = new UserPermissions(); 

                            $userPerm3->setCustcat($articleCat);
                            $userPerm3->setUserRef($id);
                            $userPerm3->setPermStatus($value);

                            $em->persist($userPerm3);
                            $em->flush();

                        } else {
                            $userPerm3->setCustcat($articleCat);
                            $userPerm3->setUserRef($id);
                            $userPerm3->setPermStatus($value);

                            $em->persist($userPerm3);
                            $em->flush();
                        } 
                            $customData = $userPerm3->getCustcat()->getCustomdata()->getValues();
                            if ( $customData != NULL ) {
                                $article = $customData[0]->getArticle();
                                $this->container->get('fos_elastica.object_persister.search.article')->replaceOne($article);    
                            } 
                            
                        $articleCats2 = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('parentRef' => $articleCat->getId()));
                        
                        foreach ($articleCats2 as $articleCat) {

                            $userPerm4 = $em->getRepository('OrthIndexBundle:UserPermissions')->findOneBy(array('userRef' => $id, 'custcatRef' => $articleCat->getId()));
                            if ( $userPerm4 == NULL ) {
                                
                                $userPerm4 = new UserPermissions(); 

                                $userPerm4->setCustcat($articleCat);
                                $userPerm4->setUserRef($id);
                                $userPerm4->setPermStatus($value);

                                $em->persist($userPerm4);
                                $em->flush();

                            } else {
                                $userPerm4->setCustcat($articleCat);
                                $userPerm4->setUserRef($id);
                                $userPerm4->setPermStatus($value);

                                $em->persist($userPerm4);
                                $em->flush();
                            } 
                            
                            $customData = $userPerm4->getCustcat()->getCustomdata()->getValues();
                            if ( $customData != NULL ) {

                                $article = $customData[0]->getArticle();
                                $this->container->get('fos_elastica.object_persister.search.article')->replaceOne($article);    
                            } 
                        }
                    
                    }
                    
                        

                } 
                    
            }

            $this->get('session')->getFlashBag()->add('notice', 'Die Änderungen wurden erfolgreich gespeichert!');
            
        }
        
        $categories = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef(), 'parentRef' => NULL));
                
        foreach ( $categories as $category ) {
            $userPermCat = new UserPermissions();  
            $userPermCat = $em->getRepository('OrthIndexBundle:UserPermissions')->findOneBy(array('userRef' => $id, 'custcatRef' => $category->getId()));
            if($userPermCat != NULL) {
                $category->setCheckedCat($userPermCat->getPermStatus());
            }
        }
        
        return $this->render('OrthIndexBundle:Account:manageuser.html.twig', array('categories' => $categories, 'users' => $users));
    }  
    
    public function deletecategoryAction($id, Request $request) {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $category = new Customcategory(); 
        
        $em = $this->getDoctrine()->getManager();
        
        $arrayToRefresh = [];
        
        $category = $em->getRepository('OrthIndexBundle:Customcategory')->findOneBy(array('customerRef' => $user->getCustomerRef(), 'id' => $id));
        if($category != NULL) {
            $arrayToRefresh[] = $id;
        }
        $subcategory = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef(), 'parentRef' => $id));
        
        if($subcategory != NULL) {
            foreach ($subcategory as $sub1) {
                $arrayToRefresh[] = $sub1->getId();

                $subcategory2 = $em->getRepository('OrthIndexBundle:Customcategory')->findBy(array('customerRef' => $user->getCustomerRef(), 'parentRef' => $sub1->getId()));
            
                if($subcategory2 != NULL) {
                    foreach ($subcategory2 as $sub2) {
                        $arrayToRefresh[] = $sub2->getId();

                    }         
                }
            }
        } 
        $articlesToRefresh = [];
        foreach ($arrayToRefresh as $catIds) {
            
            $customdataArray = $em->getRepository('OrthIndexBundle:Customerdata')->findBy(array('customCatRef' => $catIds));
            
            if($customdataArray != NULL) {
                foreach($customdataArray as $cdata) {
                    
                    $articlesToRefresh[] = $cdata->getArticle();
                    
                    $em->remove($cdata);
                    $em->flush();
                }
            }
        }
        if($articlesToRefresh != NULL) {
            foreach ($articlesToRefresh as $data) {
                $this->container->get('fos_elastica.object_persister.search.article')->replaceOne($data); 
            }
            
        }
        if(isset($subcategory) AND $subcategory != NULL) {
            foreach ($subcategory as $subRem) {
                $em->remove($subRem);
            }
        }
        if(isset($subcategory2) AND $subcategory2 != NULL) {
            foreach ($subcategory2 as $subRem2) {
                $em->remove($subRem2);
            }
        }
        
        $em->remove($category);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', 'Kategorie wurde erfolgreich gelöscht!');
        
        return $this->redirectToRoute('orth_account_config_categories');
        
    } 
    
}
