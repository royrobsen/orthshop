<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                
        $groupArray = array('1' => 'Administrator', '2' => 'Einfacher Benutzer', '3' => 'Moderator', '4' => 'Erweiterter Benutzer');
        
        $builder->add('firstName', 'text', 
                    array('label' => false))
                ->add('lastName', 'text', 
                    array('label' => false))
                ->add('email', 'text', 
                    array('label' => false))
                ->add('userGroup', 'choice', array('choices' =>$groupArray))
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