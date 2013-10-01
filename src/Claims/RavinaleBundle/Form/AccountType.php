<?php

namespace Claims\RavinaleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccountType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username', 'text', array('mapped' => false, 'label' => 'Username Ravinale'))
                ->add('password', 'text', array('mapped' => false, 'label' => 'Password Ravinale'))
        ;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'ravinale';
    }

}
