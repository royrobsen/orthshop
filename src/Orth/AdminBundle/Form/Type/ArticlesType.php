<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ArticlesType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('check', 'checkbox', array(
            'label'    => false,
            'required' => false,
            'mapped'   => false
        ));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\Articles'
        ));
    }
    public function getName()
    {
        return 'article';
    }
}