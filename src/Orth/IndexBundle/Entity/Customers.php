<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customers
 */
class Customers
{
    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $companyName1;

    /**
     * @var string
     */
    private $companyName2;

    /**
     * @var string
     */
    private $companyName3;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $zipcode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $email;

    /**
     * @var integer
     */
    private $orgapegNumber;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Customers
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return Customers
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set companyName1
     *
     * @param string $companyName1
     * @return Customers
     */
    public function setCompanyName1($companyName1)
    {
        $this->companyName1 = $companyName1;

        return $this;
    }

    /**
     * Get companyName1
     *
     * @return string 
     */
    public function getCompanyName1()
    {
        return $this->companyName1;
    }

    /**
     * Set companyName2
     *
     * @param string $companyName2
     * @return Customers
     */
    public function setCompanyName2($companyName2)
    {
        $this->companyName2 = $companyName2;

        return $this;
    }

    /**
     * Get companyName2
     *
     * @return string 
     */
    public function getCompanyName2()
    {
        return $this->companyName2;
    }

    /**
     * Set companyName3
     *
     * @param string $companyName3
     * @return Customers
     */
    public function setCompanyName3($companyName3)
    {
        $this->companyName3 = $companyName3;

        return $this;
    }

    /**
     * Get companyName3
     *
     * @return string 
     */
    public function getCompanyName3()
    {
        return $this->companyName3;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Customers
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     * @return Customers
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string 
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Customers
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Customers
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set orgapegNumber
     *
     * @param integer $orgapegNumber
     * @return Customers
     */
    public function setOrgapegNumber($orgapegNumber)
    {
        $this->orgapegNumber = $orgapegNumber;

        return $this;
    }

    /**
     * Get orgapegNumber
     *
     * @return integer 
     */
    public function getOrgapegNumber()
    {
        return $this->orgapegNumber;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var integer
     */
    private $invoiceTerm;

    /**
     * @var integer
     */
    private $deliveryTerm;


    /**
     * Set invoiceTerm
     *
     * @param integer $invoiceTerm
     * @return Customers
     */
    public function setInvoiceTerm($invoiceTerm)
    {
        $this->invoiceTerm = $invoiceTerm;

        return $this;
    }

    /**
     * Get invoiceTerm
     *
     * @return integer 
     */
    public function getInvoiceTerm()
    {
        return $this->invoiceTerm;
    }

    /**
     * Set deliveryTerm
     *
     * @param integer $deliveryTerm
     * @return Customers
     */
    public function setDeliveryTerm($deliveryTerm)
    {
        $this->deliveryTerm = $deliveryTerm;

        return $this;
    }

    /**
     * Get deliveryTerm
     *
     * @return integer 
     */
    public function getDeliveryTerm()
    {
        return $this->deliveryTerm;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $customeraddress;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->customeraddress = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add customeraddress
     *
     * @param \Orth\IndexBundle\Entity\CustomersAddresses $customeraddress
     * @return Customers
     */
    public function addCustomeraddress(\Orth\IndexBundle\Entity\CustomersAddresses $customeraddress)
    {
        $this->customeraddress[] = $customeraddress;

        return $this;
    }

    /**
     * Remove customeraddress
     *
     * @param \Orth\IndexBundle\Entity\CustomersAddresses $customeraddress
     */
    public function removeCustomeraddress(\Orth\IndexBundle\Entity\CustomersAddresses $customeraddress)
    {
        $this->customeraddress->removeElement($customeraddress);
    }

    /**
     * Get customeraddress
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCustomeraddress()
    {
        return $this->customeraddress;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $user;


    /**
     * Add user
     *
     * @param \Orth\IndexBundle\Entity\Users $user
     * @return Customers
     */
    public function addUser(\Orth\IndexBundle\Entity\Users $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Orth\IndexBundle\Entity\Users $user
     */
    public function removeUser(\Orth\IndexBundle\Entity\Users $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }
    
        /**
     * @Assert\Length(
     *     min = 6,
     *     minMessage = "Das Passwort muss mindestens 6 Zeichen haben"
     * )
     */
     public $newPassword;
     
     /**
     * Get newPassword
     *
     * @return integer 
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }
    
    /**
     * @var text
     */
    private $country;
    
    /**
     * Set country
     *
     * @param integer $country
     * @return Customers
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return integer 
     */
    public function getCountry()
    {
        return $this->country;
    }
    
}
