<?php

namespace JF\ACLBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccountType extends AbstractType {

    private $options = null;
    
    function __construct($options) {
        $this->options = $options;
    }

    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        foreach ($this->options as $key => $form) {
            $subform = $form['form'];
            $label = $form['label'];
            $builder->add(str_replace('.', '_', $key), new $subform(), array('label' => $label, 'mapped' => false));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'JF\ACLBundle\Entity\Cliente'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'jf_cliente';
    }

}
