<?php

namespace Orth\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Orth\IndexBundle\Entity\Customers;
use Orth\IndexBundle\Form\Type\CustomerType;

use Orth\IndexBundle\Entity\CustomersAddresses;

use Orth\IndexBundle\Entity\Customcategory;
use Orth\IndexBundle\Form\Type\CustomcategoryType;

use Orth\IndexBundle\Entity\Users;
use Orth\IndexBundle\Form\Type\UserType;
use Orth\IndexBundle\Form\Type\PasswordType;

use Orth\IndexBundle\Entity\UserPermissions;
use Orth\IndexBundle\Entity\Tokens;
use Orth\IndexBundle\Form\Type\AddressType;
use Orth\IndexBundle\Form\Model\ChangePassword;

class AccountController extends Controller
{
    
    // returns account root page
    public function myaccountAction() {
        
        return $this->render('OrthIndexBundle:Account:myaccount.html.twig');
        
    }
    
    // returns order history of current user
    public function orderhistoryAction() {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $em_orders = $this->getDoctrine()->getManager()->getRepository('OrthIndexBundle:Orders');
        
        $orders = $em_orders->getOrderHistory($user);

        return $this->render('OrthIndexBundle:Account:orderhistory.html.twig', array('orders' => $orders));
    }  
    
    // returns single order of current user
    public function orderAction($id) {
        
        // load security auth check
        $securityContext = $this->container->get('security.authorization_checker');

        // check if user is logged in
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
        
            
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();

            // load repository classes
            $em_orders = $em->getRepository('OrthIndexBundle:Orders');
            $em_customersaddresses = $em->getRepository('OrthIndexBundle:CustomersAddresses');
            $em_shoppingCart = $em->getRepository('OrthIndexBundle:ShoppingCart');
            
            // find order
            $order = $em_orders->getOrder($user, $id);

            // find addresses
            $invoiceAddress = $em_customersaddresses->getAddressById($user, $order[0]->getInvoiceAdrRef()); 
            $shippingAddress = $em_customersaddresses->getAddressById($user, $order[0]->getShippingAdrRef()); 

            // build cart of the order
            $cartItems = $em_shoppingCart->buildCart($order[0]->getPositions(), $user);
        
        } else { 
            // force to exit if user is not logged in
            exit; 
        }
        
        return $this->render('OrthIndexBundle:Account:order.html.twig', 
                array('order' => $order, 'invAdr' => $invoiceAddress, 
                      'delAdr' => $shippingAddress, 'cart' => $cartItems));
        
    }  
  
    // returns config page for account (navigation here only)
    public function configAction() {
    
        return $this->render('OrthIndexBundle:Account:config.html.twig');
        
    }  
    
    // returns page with lists of customers address NOTE: CUSTOMERS, NOT USERS!
    public function myaddressAction() {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
                
        $em = $this->getDoctrine()->getManager();
        
        $em_customersaddresses = $em->getRepository('OrthIndexBundle:CustomersAddresses');
        
        // returns array of primary, delivery and all other addresses by customer
        $addresses = $em_customersaddresses->getAddresses($user);
                  
        return $this->render('OrthIndexBundle:Account:myaddress.html.twig', array('addresses' => $addresses));
        
    } 
    
    // returns page with form to add address to a customer
    public function addaddressAction(Request $request) {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        // create new address object
        $addresses = new CustomersAddresses(); 

        // create form
        $form = $this->createForm(new AddressType(), $addresses);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $addresses->setCustomerRef($user->getCustomerRef());
            $addresses->setPrimaryAddress(0);
            $addresses->setDefaultDeliveryAddress(0);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($addresses);
            $em->flush();
            
            // generate 'success' flash bag message to show for user
            $this->get('session')->getFlashBag()->add('notice', 'Adresse wurde erfolgreich hinzugefügt!');
            
            // redirect to account address list
            return $this->redirectToRoute('orth_account_myaddress');
        }
        
        return $this->render('OrthIndexBundle:Account:addaddress.html.twig', array('form' => $form->createView()));
    } 
    
    // delete address by id; returns no page
    public function deleteAddressAction($id) {
          
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $em_customersaddresses = $em->getRepository('OrthIndexBundle:CustomersAddresses');
        
        // returns address object by customer and (address)id
        $address = $em_customersaddresses->getAddressById($user, $id);
        
        // deleting address from database
        $em->remove($address);
        $em->flush();
        
        // generate 'success' flash bag message to show for user
        $this->get('session')->getFlashBag()->add('notice', 'Die Adresse wurde erfolgreich gelöscht!');
        
        // redirect to account address list
        return $this->redirectToRoute('orth_account_myaddress');
        
    } 
    
    // returns page with form to edit address of a customer
    public function editaddressAction($id, Request $request) {
           
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $em_customersaddresses = $em->getRepository('OrthIndexBundle:CustomersAddresses');
        
        // returns address object by customer and (address)id
        $address = $em_customersaddresses->getAddressById($user, $id);
        
        // check if address exists, if not show fail message to user and redirect
        if($address == NULL) {
            // generate 'fail' flash bag message to show for user
            $this->get('session')->getFlashBag()->add('warning', 'Adresse existiert nicht!');
            
            // redirect to account address list
            return $this->redirectToRoute('orth_account_myaddress');
        }
        
        // create form
        $form = $this->createForm(new AddressType(), $address);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            // save changes to database
            $em->persist($address);
            $em->flush();
            
            // generate 'success' flash bag message to show for user
            $this->get('session')->getFlashBag()->add('notice', 'Adresse wurde erfolgreich gespeichert!');
            
            // redirect to account address list
            return $this->redirectToRoute('orth_account_myaddress');
        }
        
        return $this->render('OrthIndexBundle:Account:editaddress.html.twig', array('form' => $form->createView()));
        
    } 
    
    // returns page with form of personal info, such as firstname, lastname, email
    public function personalinfoAction(Request $request) {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
         
        // create form
        $form = $this->createForm(new UserType(), $user);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            // save changes to database
            $em->persist($user);
            $em->flush();
            
            // generate 'success' flash bag message to show for user
            $this->get('session')->getFlashBag()->add('notice', 'Ihre Daten wurden erfolgreich gespeichert!');
        }
        
        return $this->render('OrthIndexBundle:Account:personalinfo.html.twig', array('form' => $form->createView()));
    } 
    
    // returns page with lists of articles to be remembered
    public function wishlistAction() {
        
        return $this->render('OrthIndexBundle:Account:wishlist.html.twig');
        
    } 
    
    // returns page to change users password, no further auth required, since user is already logged in
    public function changePasswordAction(Request $request) {
        
      // set empty error variable
      $error = "";
      
      // create new ChangePassword object
      $changePasswordModel = new ChangePassword();
      
      // create form
      $form = $this->createForm(new PasswordType(), $changePasswordModel);
            
      $form->handleRequest($request);

      if ($form->isValid()) {
          
          // loads current user, '.anon' if not logged in
          $user = $this->get('security.token_storage')->getToken()->getUser();
          
          $encoderFactory = $this->get('security.encoder_factory');
          $encoder = $encoderFactory->getEncoder($user);
          
          $salt = '$2a$12$uWepESKverBsrLAuOPY';
          
          // returns data of the form
          $newPw = $form->getData();

          // encode new password with salt
          $password = $encoder->encodePassword($newPw->getNewPassword(), $salt);
          $user->setPasskey($password);
          
          $em = $this->getDoctrine()->getManager();
          
          // save changes to database
          $em->persist($user);
          $em->flush();
          
          // generate 'success' flash bag message to show for user
          $this->get('session')->getFlashBag()->add('success', 'Ihr Passwort wurde erfolgreich geändert. Sie können sich nun damit einloggen!');
          
      }
          return $this->render('OrthIndexBundle:Account:changePw.html.twig', array(
          'form' => $form->createView(), 'error' => $error));
    }
    
    // returns config page for custom categories
    public function configCategoriesAction() {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
                
        $em = $this->getDoctrine()->getManager();
        $em_customercategory = $em->getRepository('OrthIndexBundle:Customcategory');
        
        // returns array of objects of categories with subarrays of children categories by customer
        $categories = $em_customercategory->getCategoryCustom($user);
                
        return $this->render('OrthIndexBundle:Account:categories.html.twig', array('categories' => $categories));
        
    }
    
    // returns page with form to add a new custom main category
    public function addcategoryAction(Request $request) {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        // create new category object
        $category = new Customcategory(); 
        
        // create new userpermissions object
        $userPerm = new UserPermissions(); 
        
        // create new form 
        $form = $this->createForm(new CustomcategoryType(), $category);
        
        // remove unnecessary fields from form object, only category name remains
        $form->remove('parentRef');
        $form->remove('userRef');
        $form->remove('customerRef');
        
        $form->handleRequest($request);
        
        // check if form is valid
        if ($form->isValid()) {
            
            $category->setCustomerRef($user->getCustomerRef());
            $category->setUserRef($user->getId());
            
            // this has to be NULL because it is a main category with no parent category
            $category->setParentRef(NULL);
            
            // creates standard user permission for this category, it will be visible for the current user
            $userPerm->setUserRef($user->getId());
            $userPerm->setCustcat($category);
            $userPerm->setPermStatus(1);
            
            $em = $this->getDoctrine()->getManager();
            
            // save objects to database
            $em->persist($category);
            $em->persist($userPerm);
            $em->flush();
            
            // generate 'success' flash bag message to show for user
            $this->get('session')->getFlashBag()->add('notice', 'Kategorie wurde erfolgreich hinzugefügt!');
            
            // redirect to categories confing page
            return $this->redirectToRoute('orth_account_config_categories');
        }
        
        return $this->render('OrthIndexBundle:Account:addcategory.html.twig', array('form' => $form->createView()));
    } 
    
    // returns form to add a sub (child) category
    public function addsubcategoryAction(Request $request, $id) {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        // create new category object
        $category = new Customcategory(); 
        
        // create new userpermissions object
        $userPerm = new UserPermissions(); 
        
        // create new form
        $form = $this->createForm(new CustomcategoryType(), $category);
        
        // remove unnecessary fields from form object, only category name remains
        $form->remove('parentRef');
        $form->remove('userRef');
        $form->remove('customerRef');
        
        $form->handleRequest($request);
        
        // check if form is valid
        if ($form->isValid()) {
            
            $category->setCustomerRef($user->getCustomerRef());
            $category->setUserRef($user->getId());
            // sets id of parent category
            $category->setParentRef($id);
            
            $userPerm->setUserRef($user->getId());
            $userPerm->setCustcat($category);
            $userPerm->setPermStatus(1);
            
            $em = $this->getDoctrine()->getManager();
            
            // save objects to database
            $em->persist($category);
            $em->persist($userPerm);
            $em->flush();
            
            // generate 'success' flash bag message to show for user
            $this->get('session')->getFlashBag()->add('notice', 'Kategorie wurde erfolgreich hinzugefügt!');
            
            // redirect to categories confing page
            return $this->redirectToRoute('orth_account_config_categories');
        }
        
        return $this->render('OrthIndexBundle:Account:addcategory.html.twig', array('form' => $form->createView()));
    } 
        
    // returns form to edit category name
    public function categoryAction(Request $request, $id) {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $em_customercategory = $em->getRepository('OrthIndexBundle:Customcategory');
        // get 'one or null result' by id and customer
        $category = $em_customercategory->getCategoryCustomById($id, $user);
        
        // check if the category exist and if the user share the same customer id with the category, else an exception is thrown
        if ( $category == NULL ) {
            throw new \Exception();
        }
        
        // create new form
        $form = $this->createForm(new CustomcategoryType(), $category);
        
        // remove unnecessary fields from form object, only category name remains
        $form->remove('parentRef');
        $form->remove('userRef');
        $form->remove('customerRef');
        
        $form->handleRequest($request);
        
        // check if form is valid
        if ($form->isValid()) {
            
             // save objects to database
            $em->persist($category);
            $em->flush();
            
            // generate 'success' flash bag message to show for user
            $this->get('session')->getFlashBag()->add('notice', 'Kategorie wurde erfolgreich umbenannt!');
            
            // redirect to categories confing page
            return $this->redirectToRoute('orth_account_config_categories');
        }
        
        return $this->render('OrthIndexBundle:Account:category.html.twig', array('form' => $form->createView()));
    }  
        
    // returns page with list of users with same customer id
    public function myuserAction() {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();
                
        $em = $this->getDoctrine()->getManager();
        $em_user = $em->getRepository('OrthIndexBundle:Users');
        
        // get users by customer id
        $users = $em_user->getUsersByCustomer($user);
             
        return $this->render('OrthIndexBundle:Account:myusers.html.twig', array('users' => $users, 'loggedInUser' => $user));
    }  
    
    // returns page with form to edit single user with same customer id
    public function myusereditAction($id, Request $request) {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $em_user = $em->getRepository('OrthIndexBundle:Users');
        
        // get one user by customer and provided id
        $users = $em_user->getOneUserByCustomerAndId($user, $id);
                             
        // create new form
        $form = $this->createForm(new UserType(), $users);
        
        $form->handleRequest($request);

        // check if form is valid
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            // save object to databases
            $em->persist($users);
            $em->flush();
            
            // generate 'success' flash bag message to show for user
            $this->get('session')->getFlashBag()->add('notice', 'Ihre Daten wurden erfolgreich gespeichert!');
        }
        
        return $this->render('OrthIndexBundle:Account:edituser.html.twig', array('form' => $form->createView(), 'users' => $users));
        
    }  
    
    // returns page to edit sub users permissions, such as visible customer categories
    public function myusermanagerAction($id, Request $request) {
        
        // loads current user, '.anon' if not logged in
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $em_user = $em->getRepository('OrthIndexBundle:Users');
        $em_userPerm = $em->getRepository('OrthIndexBundle:UserPermissions');
        $em_customcategory = $em->getRepository('OrthIndexBundle:Customcategory');
        
        // get one user by customer and provided id
        $users = $em_user->getOneUserByCustomerAndId($user, $id);
                                            
        if ( $request->request->all()) {

            $formPost = $request->request->all();
  
            foreach ($formPost as $key => $value) {
                
                   $userPerm = $em_userPerm->getUsersPermission($id,$key);

                   $custCat = $em_customcategory->getCategoryCustomById($id, $user);
    
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
    
    public function signupAction(Request $request) {
        
        $customer = new Customers();
        
        $em = $this->getDoctrine()->getManager();
                             
        $form = $this->createForm(new CustomerType(), $customer);

        $form->handleRequest($request);
        
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $formData = $form->getData();
            
            $checkMail = $em->getRepository('OrthIndexBundle:Users')->findOneBy(array('email' => $formData->getEmail()));
            if($checkMail == NULL ) {
                
                $user = new Users(); 
                $customerAddress = new CustomersAddresses();
                $token = new Tokens(); 
                
                $customer->setOrgapegNumber(0);
                $customer->setInvoiceTerm(0);
                $customer->setDeliveryTerm(0);

                $customerAddress->setAddressTitle('Standardadresse');
                $customerAddress->setCity($formData->getCity());
                $customerAddress->setCompanyName1($formData->getCompanyName1());
                $customerAddress->setCompanyName2($formData->getCompanyName2());
                $customerAddress->setCompanyName3($formData->getCompanyName3());
                $customerAddress->setCountry(1);
                $customerAddress->setCustomerRef($customer);
                $customerAddress->setCustomer($customer);
                $customerAddress->setDefaultDeliveryAddress(1);
                $customerAddress->setPrimaryAddress(1);
                $customerAddress->setFirstName($formData->getFirstName());
                $customerAddress->setLastName($formData->getLastName());
                $customerAddress->setStreet($formData->getStreet());
                $customerAddress->setStreet2('');
                $customerAddress->setZipcode($formData->getZipcode());

                function generateRandomString($length = 10) {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $charactersLength = strlen($characters);
                    $randomString = '';
                    for ($i = 0; $i < $length; $i++) {
                        $randomString .= $characters[rand(0, $charactersLength - 1)];
                    }
                    return $randomString;
                }

                $encoderFactory = $this->get('security.encoder_factory');
                $encoder = $encoderFactory->getEncoder($user);

                $salt = '$2a$12$uWepESKverBsrLAuOPY';

                $passkeyHash = $encoder->encodePassword($formData->getNewPassword(), $salt);

                $user->setEmail($formData->getEmail());
                $user->setFirstName($formData->getFirstName());
                $user->setLastName($formData->getLastName());
                $user->setUserGroup(2);
                $user->setPasskey($passkeyHash);
                $user->setCustomer($customer);
                $user->setCustomerRef($customer);
                
                $datetime = new \DateTime('tomorrow');
                $datetime->format('Y-m-d H:i:s');
                
                $token->setToken(md5(uniqid()));
                $token->setExpDate($datetime);
                $token->setUser($user);
                
                $em->persist($customer);
                $em->persist($customerAddress);
                $em->persist($user);
                $em->persist($token);
                $em->flush();
                
                $message = \Swift_Message::newInstance()
                    ->setSubject('Registrierung im OrthShop')
                    ->setFrom('no-reply@ute-orth.de')
                    ->setTo($formData->getEmail())
                    ->setContentType("text/html")
                    ->setBody(
                        $this->renderView(
                            'OrthIndexBundle:Mail:registrationMail.html.twig',
                            array('user' => $user, 'token' => $token),
                        'text/html'
                        )
                    )
                    ;
                    $this->get('mailer')->send($message);
                    
                $this->get('session')->getFlashBag()->add('notice', 'Vielen Dank für Ihre Registrierung. Wir haben Ihnen eine E-Mail zur Bestätigung geschickt!');

                return $this->redirect($this->generateUrl('orth_index_account'));
                
            } else {
                
                $this->get('session')->getFlashBag()->add('warning', 'Die E-Mailadresse existiert bereits! Bitte verwenden Sie eine andere E-Mailadresse oder melden Sie sich an.');

            }   
        }
        
        return $this->render('OrthIndexBundle:Index:signup.html.twig', array('form' => $form->createView()));
        
    }
    
    public function registerAction($token) {
            
        $em = $this->getDoctrine()->getManager();
        
        $checkToken = $em->getRepository('OrthIndexBundle:Tokens')->findOneBy(array('token' => $token));
        
        if( $checkToken != NULL AND $checkToken->getExpDate() > new \DateTime()) {
        
            $user = new Users(); 
            
            $user = $em->getRepository('OrthIndexBundle:Users')->findOneBy(array('id' => $checkToken->getUserRef()));
            $user->setActive(true);
            
            $em->remove($checkToken);
            $em->persist($user);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('notice', 'Registrierung erfolgreich! Sie können sich nun anmelden.');

        } else {
            
            $this->get('session')->getFlashBag()->add('alert', 'Link nicht mehr gültig! Bitte fordern Sie ein neues Passwort an!');

        }
        
        return $this->redirect($this->generateUrl('orth_index_account'));
    }
    
}
