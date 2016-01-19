<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomersAddresses
 */
class CustomersAddresses
{
    /**
     * @var integer
     */
    private $customerRef;

    /**
     * @var boolean
     */
    private $primaryAddress;

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
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

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
    private $street2;


    /**
     * @var boolean
     */
    private $defaultDeliveryAddress;
    
    /**
     * @var text
     */
    private $country;

    /**
     * @var string
     */
    private $addressTitle;
    
    /**
     * @var integer
     */
    private $id;


    /**
     * Set customerRef
     *
     * @param integer $customerRef
     * @return CustomersAddresses
     */
    public function setCustomerRef($customerRef)
    {
        $this->customerRef = $customerRef;

        return $this;
    }

    /**
     * Get customerRef
     *
     * @return integer 
     */
    public function getCustomerRef()
    {
        return $this->customerRef;
    }

    /**
     * Set primaryAddress
     *
     * @param boolean $primaryAddress
     * @return CustomersAddresses
     */
    public function setPrimaryAddress($primaryAddress)
    {
        $this->primaryAddress = $primaryAddress;

        return $this;
    }

    /**
     * Get primaryAddress
     *
     * @return boolean 
     */
    public function getPrimaryAddress()
    {
        return $this->primaryAddress;
    }

    /**
     * Set defaultDeliveryAddress
     *
     * @param boolean $defaultDeliveryAddress
     * @return CustomersAddresses
     */
    public function setDefaultDeliveryAddress($defaultDeliveryAddress)
    {
        $this->defaultDeliveryAddress = $defaultDeliveryAddress;

        return $this;
    }

    /**
     * Get defaultDeliveryAddress
     *
     * @return boolean 
     */
    public function getDefaultDeliveryAddress()
    {
        return $this->defaultDeliveryAddress;
    }
    
    /**
     * Set companyName1
     *
     * @param string $companyName1
     * @return CustomersAddresses
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
     * @return CustomersAddresses
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
     * @return CustomersAddresses
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
     * Set firstName
     *
     * @param string $firstName
     * @return CustomersAddresses
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
     * Set lastName
     *
     * @param string $lastName
     * @return CustomersAddresses
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
     * Set street
     *
     * @param string $street
     * @return CustomersAddresses
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
     * @return CustomersAddresses
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
     * @return CustomersAddresses
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
     * Set street2
     *
     * @param string $street2
     * @return CustomersAddresses
     */
    public function setStreet2($street2)
    {
        $this->street2 = $street2;

        return $this;
    }

    /**
     * Get street2
     *
     * @return string 
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * Set country
     *
     * @param integer $country
     * @return CustomersAddresses
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

    /**
     * Set addressTitle
     *
     * @param string $addressTitle
     * @return CustomersAddresses
     */
    public function setAddressTitle($addressTitle)
    {
        $this->addressTitle = $addressTitle;

        return $this;
    }

    /**
     * Get addressTitle
     *
     * @return string 
     */
    public function getAddressTitle()
    {
        return $this->addressTitle;
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
}
