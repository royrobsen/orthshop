<?php

namespace Orth\IndexBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomcategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
               
        $builder->add('categoryName', 'text', 
                    array('label' => false))
                ->add('parentRef', 'integer',
                    array('label' => false))
                ->add('userRef', 'integer',
                    array('label' => false))
                ->add('customerRef', 'integer',
                    array('label' => false))
                ->add('save', 'submit', array('label' => 'Kategorie speichern'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\Customcategory',
        ));
    }
    public function getName()
    {
        return 'customCategory';
    }
}