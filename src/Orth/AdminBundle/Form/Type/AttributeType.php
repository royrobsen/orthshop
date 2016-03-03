<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AttributeType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('attributeName', 'text', 
                    array('label' => false))
                ->add('save', 'submit', array('label' => 'Speichern'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\ArticleAttributes'
        ));
    }
    public function getName()
    {
        return 'attributeName';
    }
}