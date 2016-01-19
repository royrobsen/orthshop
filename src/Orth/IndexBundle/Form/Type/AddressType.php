<?php

namespace Orth\IndexBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $countryArray = array('1' => 'Deutschland', '2' => 'Österreich', '3' => 'Schweiz');
        
        $builder->add('companyName1', 'text', 
                    array('label' => false))
                ->add('companyName2', 'text', 
                    array('label' => false))
                ->add('companyName3', 'text', 
                    array('label' => false))
                ->add('firstName', 'text', 
                    array('label' => false))
                ->add('lastName', 'text', 
                    array('label' => false))
                ->add('street', 'text', 
                    array('label' => false))
                ->add('zipcode', 'text', 
                    array('label' => false))
                ->add('city', 'text', 
                    array('label' => false))
                ->add('street2', 'text', 
                    array('label' => false))
                ->add('addressTitle', 'text', 
                    array('label' => false))
                ->add('firstName', 'text', 
                    array('label' => false))
                ->add('country', 'choice', array('choices' =>$countryArray,
                'data' => 1))
                ->add('save', 'submit', array('label' => 'Adresse speichern'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\CustomersAddresses',
        ));
    }
    public function getName()
    {
        return 'customerAddresses';
    }
}