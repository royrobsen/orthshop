<?php

namespace Orth\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
                ->add('attachment', 'file',
                array('label' => false, 'required' => false, 'multiple' => true))
                ->add('category', 'entity', array(
                'class' => 'OrthIndexBundle:Categories',
                'group_by' => 'parentName',
                'property' => 'categoryName',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repo) {
                     $qb = $repo->createQueryBuilder('c');
                     $qb->andWhere('c.parent IS NOT NULL');
                     $qb->orderBy('c.categoryName', 'ASC');

                     return $qb;
                }
            ))
//                ->add('category', 'entity', array(
//                    'class' => 'OrthIndexBundle:Categories', 'property' => 'categoryName',
//                    'query_builder' => function (EntityRepository $er) {
//                        return $er->createQueryBuilder('c')
//                            ->select('c, c1, c2')
//                            ->leftJoin('c.children', 'c1')
//                            ->leftJoin('c1.children', 'c2')
//                            ->orderBy('c.categoryName', 'ASC');
//                    },
//                ))
                ->add('save', 'submit', array('label' => 'Speichern'));
        
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $article = $event->getData();
            $form = $event->getForm();
            if (!$article || null === $article->getId()) {
            $form->add('attrName', 'entity', array(
                    'class' => 'OrthIndexBundle:ArticleAttributes',
                    'property' => 'attributeName', 'multiple' => true, 'mapped' => false,
                ));
        } else {
            $form->add('variants', 'collection', array('type' => new VariantsType()));
        }
        });
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Entity\Articles', 'attrName' => 'Orth\IndexBundle\Entity\ArticleAttributes', 'mainCat' => 'Orth\IndexBundle\Entity\Categories'
        ));
    }
    public function getName()
    {
        return 'article';
    }
}