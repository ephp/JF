<?php

namespace JF\ACLBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseForm;

class ProfileFormType extends BaseForm {

    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('sigla', null, array('attr' => array('tabindex' => 1)))
                ->add('firstname', null, array('label' => 'Nome', 'attr' => array('tabindex' => 3)))
                ->add('lastname', null, array('label' => 'Cognome', 'attr' => array('tabindex' => 4)))
                ->add('email', null, array('attr' => array('tabindex' => 2)))
                ->add('telefono', null, array('attr' => array('tabindex' => 5)))
//                ->add('birthday')
//                ->add('avatar')
        ;
        
    }

}
