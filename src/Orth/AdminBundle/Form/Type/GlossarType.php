<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GlossarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                
        $alphabet = array_combine(range('A','Z'),range('A','Z'));
        
        $builder->add('subject', 'text', 
                    array('label' => false))
                ->add('description', 'textarea', 
                    array('label' => false))
                ->add('letter', 'choice', array('choices' =>$alphabet))
                ->add('save', 'submit', array('label' => 'Speichern'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\Glossar',
        ));
    }
    public function getName()
    {
        return 'glossar';
    }
}