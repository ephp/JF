<?php

namespace Claims\HBundle\Form;

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
                ->add('definiti', 'textarea', array('mapped' => false, 'label' => 'Status definiti (separati da virgola) per chiusura automatica sinistri', 'required' => false))
        ;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'email_hospital';
    }

}
