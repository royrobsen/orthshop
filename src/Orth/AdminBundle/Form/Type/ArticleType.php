<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                
        $builder->add('shortName', 'text', 
                    array('label' => false))
                ->add('shortDescription', 'text', 
                    array('label' => false))
                ->add('longDescription', 'textarea', 
                    array('label' => false))
                ->add('variants', 'collection', array('type' => new VariantsType()))
                ->add('save', 'submit', array('label' => 'Speichern'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\Articles', 'attrName' => 'Orth\IndexBundle\Entity\ArticleAttributeValues'
        ));
    }
    public function getName()
    {
        return 'article';
    }
}