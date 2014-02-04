<?php

namespace Claims\HAuditBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('gruppo', null, array(
                    'label' => 'Gruppo',
                ))
                ->add('ordine', null, array(
                    'label' => 'Ordine',
                ))
                ->add('question', null, array(
                    'label' => 'Question',
                ))
                ->add('example', null, array(
                    'label' => 'Question Note',
                ))
                ->add('domanda', null, array(
                    'required' => false,
                    'label' => 'Domanda',
                ))
                ->add('esempio', null, array(
                    'required' => false,
                    'label' => 'Note domanda',
                ))
                ->add('type', 'choice', array(
                    'choices' => array(
                        'text' => 'Testo',
                        'textarea' => 'Testo libero',
                        'number' => 'Numero',
                        'date' => 'Data',
                        'money' => 'Importo (€)',
                        'select' => 'Menu a tendina',
                        'checkbox' => 'Scelta multipla',
                        'fx' => 'Formula (TODO)',
                    ),
                    'label' => 'Tipo di campo per la risposta',
                ))
                ->add('prepopulate', 'choice', array(
                    'required' => false,
                    'choices' => array(
                        '' => '',
                        'claimant' => 'Nome Claimnnt',
                        'tpa' => 'TPA',
                        'dol' => 'DOL',
                        'don' => 'DON',
                        'mfRef' => 'MF Ref',
                        'ospedale' => 'Nome Ospedale',
                        'dsCode' => 'DS Code',
                    ),
                    'label' => 'Compilazione automatica',
                ))
                ->add('options', 'textarea', array(
                    'required' => false,
                    'label' => 'Opzioni risposte (una risposta per riga)',
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Claims\HAuditBundle\Entity\Question'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'claims_hauditbundle_question';
    }

}
