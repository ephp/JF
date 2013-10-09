<?php

namespace SLC\ImportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BaseType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('url_base', 'text', array('mapped' => false, 'label' => 'URL di JF-Claims'))
        ;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'jfclaims';
    }

}
