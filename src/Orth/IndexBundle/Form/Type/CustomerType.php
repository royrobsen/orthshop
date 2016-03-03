<?php

namespace Orth\IndexBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $countryArray = array('1' => 'Deutschland', '2' => 'Österreich', '3' => 'Schweiz');   
        
        $builder->add('companyName1', 'text', 
                    array('label' => false, 'required' => false))
                ->add('companyName2', 'text', 
                    array('label' => false, 'required' => false))
                ->add('companyName3', 'text', 
                    array('label' => false, 'required' => false))
                ->add('firstName', 'text', 
                    array('label' => false))
                ->add('email', 'email', 
                    array('label' => false))
                ->add('lastName', 'text', 
                    array('label' => false))
                ->add('street', 'text', 
                    array('label' => false))
                ->add('zipcode', 'text', 
                    array('label' => false))
                ->add('city', 'text', 
                    array('label' => false))
                ->add('firstName', 'text', 
                    array('label' => false))
                ->add('newPassword', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'Passwörter stimmen nicht überein',
            'required' => true,
            'first_options'  => array('label' => 'Neues Passwort'),
            'second_options' => array('label' => 'Wiederholen'),
                ))
                ->add('country', 'choice', array('choices' =>$countryArray,
                'data' => 1))
                ->add('save', 'submit', array('label' => 'Adresse speichern'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\Customers',
        ));
    }
    public function getName()
    {
        return 'customer';
    }
}