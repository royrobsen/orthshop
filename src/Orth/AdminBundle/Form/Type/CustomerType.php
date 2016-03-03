<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CustomerType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $countryArray = array('1' => 'Deutschland', '2' => 'Österreich', '3' => 'Schweiz');
        $invArray = array('0' => 'Rechnung', '1' => 'Vorkasse');
        $delArray = array('0' => 'UPS', '1' => 'Abholung', '3' => 'Frei Haus');
        
        $builder->add('firstName', 'text', 
                    array('label' => false))
                ->add('lastName', 'text', 
                    array('label' => false)) 
                ->add('street', 'text', 
                    array('label' => false)) 
                ->add('zipcode', 'text', 
                    array('label' => false)) 
                ->add('city', 'text', 
                    array('label' => false)) 
                ->add('email', 'email', 
                    array('label' => false))
                ->add('orgapegNumber', 'text', 
                    array('label' => false, 'required' => false))
                ->add('country', 'choice', array('choices' =>$countryArray))
                ->add('companyName1', 'text', 
                    array('label' => false, 'required' => false))
                ->add('companyName2', 'text', 
                    array('label' => false, 'required' => false))
                ->add('companyName3', 'text', 
                    array('label' => false, 'required' => false))
                ->add('invoiceTerm', 'choice', array('choices' =>$invArray))
                ->add('deliveryTerm', 'choice', array('choices' =>$delArray))
                ->add('save', 'submit', array('label' => 'Speichern'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\Customers'
        ));
    }
    public function getName()
    {
        return 'customer';
    }
}