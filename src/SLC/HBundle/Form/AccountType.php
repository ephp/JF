<?php

namespace SLC\HBundle\Form;

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
                ->add('sigle', 'text', array('mapped' => false, 'label' => 'Sigle ospedali da escludere separate da virgola', 'required' => false))
        ;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'slc_hospital';
    }

}
