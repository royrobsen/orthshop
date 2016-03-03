<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MoveArticlesType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('category', 'entity', array(
                'class' => 'OrthIndexBundle:Categories',
                'group_by' => 'parentName',
                'property' => 'categoryName',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repo) {
                     $qb = $repo->createQueryBuilder('c');
                     $qb->andWhere('c.parent IS NOT NULL');
                     $qb->orderBy('c.categoryName', 'ASC');

                     return $qb;
                }))
                ->add('save', 'submit', array('label' => 'Verschieben'));
        
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $resolver->setDefaults(array(
            'data_class' => NULL,
            'csrf_protection' => false));
    }
    public function getName()
    {
        return 'article';
    }
}