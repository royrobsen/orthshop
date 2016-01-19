<?php

namespace Orth\IndexBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('oldPassword', 'password');
        $builder->add('newPassword', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'Passwörter stimmen nicht überein',
            'required' => true,
            'first_options'  => array('label' => 'Neues Passwort'),
            'second_options' => array('label' => 'Wiederholen'),
                ))
                ->add('save', 'submit', array('label' => 'Passwort ändern'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orth\IndexBundle\Form\Model\ChangePassword',
        ));
    }

    public function getName()
    {
        return 'change_passwd';
    }
}