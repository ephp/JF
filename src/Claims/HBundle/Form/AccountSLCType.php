<?php

namespace Claims\HBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccountSLCType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('definiti', 'textarea', array('mapped' => false, 'label' => 'Status definiti (separati da virgola) per chiusura automatica sinistri', 'required' => false))
                ->add('server', 'text', array('mapped' => false, 'label' => 'Server di posta', 'required' => false))
                ->add('port', 'text', array('mapped' => false, 'label' => 'Porta del server', 'required' => false))
                ->add('protocol', 'text', array('mapped' => false, 'label' => 'Protocollo di posta', 'required' => false))
                ->add('username', 'text', array('mapped' => false, 'label' => 'Username Email', 'required' => false))
                ->add('password', 'text', array('mapped' => false, 'label' => 'Password Email', 'required' => false))
                ->add('label_contec', 'text', array('mapped' => false, 'label' => 'Label email da Contec', 'required' => false))
                ->add('label_ravinale', 'text', array('mapped' => false, 'label' => 'Label email da Ravinale', 'required' => false))
                ->add('label_manuala', 'text', array('mapped' => false, 'label' => 'Label email generica', 'required' => false))
                ->add('label_cd_richieste', 'text', array('mapped' => false, 'label' => 'Label email richiete', 'required' => false))
                ->add('label_cd_risposte', 'text', array('mapped' => false, 'label' => 'Label email risposte', 'required' => false))
        ;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'email_hospital';
    }

}
