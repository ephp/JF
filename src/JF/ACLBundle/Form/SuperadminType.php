<?php

namespace JF\ACLBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SuperadminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cliente', null, array('attr' => array('readonly' => 'readonly', 'tabindex' => 1)))
            ->add('sigla', null, array('attr' => array('tabindex' => 2)))
            ->add('firstname', null, array('label' => 'Nome', 'attr' => array('tabindex' => 3)))
            ->add('lastname', null, array('label' => 'Cognome', 'attr' => array('tabindex' => 4)))
            ->add('email', null, array('attr' => array('tabindex' => 5)))
            ->add('telefono', null, array('attr' => array('tabindex' => 6)))
        ;
    }

    public function getName()
    {
        return 'jf_superadmin';
    }
}
