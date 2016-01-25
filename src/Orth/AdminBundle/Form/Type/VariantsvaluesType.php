<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class VariantsvaluesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                
        $builder->add('attributeValue', 'text', 
                    array('label' => false))
                ->add('attributeUnit', 'text', 
                    array('label' => false, 'required' => false));
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\ArticleAttributeValues', 
        ));
    }
    public function getName()
    {
        return 'variantsvalues';
    }
    
}