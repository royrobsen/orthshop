<?php

namespace Orth\IndexBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
               
        $builder->add('permStatus', 'entity', array(
            'required'      => false,
            'class'         => 'OrthIndexBundle:UserPermissions',
            'property'      => 'permStatus',
            'property_path' => '[id]', # in square brackets!
            'multiple'      => true,
            'expanded'      => true
        ))
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
        return 'users';
    }
}