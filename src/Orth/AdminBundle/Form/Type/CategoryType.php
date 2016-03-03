<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CategoryType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('categoryName', 'text', 
                    array('label' => false))
                ->add('save', 'submit', array('label' => 'Speichern'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\Categories'
        ));
    }
    public function getName()
    {
        return 'category';
    }
}