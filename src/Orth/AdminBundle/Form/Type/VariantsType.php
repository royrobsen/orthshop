<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VariantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                
        $builder->add('supplierArticleNumber', 'text', 
                    array('label' => false))
                ->add('price', 'text', 
                    array('label' => false))
                ->add('variantvalues', 'collection', array('type' => new VariantsvaluesType()));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\ArticleSuppliers',
        ));
    }
    public function getName()
    {
        return 'variants';
    }
}