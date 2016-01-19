<?php

namespace Orth\IndexBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('save' , 'submit')
            ->setMethod('GET')  
            ->add('query', 'text')
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IndexBundle\Entity\Products',
        ));
    }

    public function getName()
    {
        return '';
    }
}