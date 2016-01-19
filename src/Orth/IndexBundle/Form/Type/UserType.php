<?php

namespace Orth\IndexBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $countryArray = array('1' => 'Deutschland', '2' => 'Österreich', '3' => 'Schweiz');
        
        $builder->add('firstName', 'text', 
                    array('label' => false))
                ->add('lastName', 'text', 
                    array('label' => false))
                ->add('email', 'text', 
                    array('label' => false))
                ->add('save', 'submit', array('label' => 'Speichern'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\Users',
        ));
    }
    public function getName()
    {
        return 'users';
    }
}