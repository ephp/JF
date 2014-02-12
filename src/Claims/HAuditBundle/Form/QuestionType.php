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
                    'label' => 'Group',
                ))
                ->add('sottogruppo', null, array(
                    'label' => 'Subgroup',
                ))
                ->add('ordine', null, array(
                    'label' => 'Orger',
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
                ->add('anteprima', null, array(
                    'required' => false,
                    'label' => 'Preview',
                ))
                ->add('ricerca', null, array(
                    'required' => false,
                    'label' => 'Searchable',
                ))
                ->add('type', 'choice', array(
                    'choices' => array(
                        'text' => 'Testo',
                        'textarea' => 'Testo libero',
                        'number' => 'Numero',
                        'percent' => 'Percentuale',
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
                        'reserve' => 'Reserve',
                        'proReserve' => 'Pro Reserve',
                        'gruppo' => 'Riferimento TPA',
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
