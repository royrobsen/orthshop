<?php

namespace Orth\IndexBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserPermType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
               
        $builder->add('permStatus', 'checkbox', 
                    array('label' => false, 'required' => true))
                ->add('userRef', 'integer', 
                    array('label' => false))
                ->add('custcatRef', 'integer', 
                    array('label' => false))
                ->add('save', 'submit', array('label' => 'Speichern'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\UserPermissions',
        ));
    }
    public function getName()
    {
        return 'userPerm';
    }
}