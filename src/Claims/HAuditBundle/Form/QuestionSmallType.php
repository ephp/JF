<?php

namespace Claims\HAuditBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionSmallType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
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
                        'text' => 'Text',
                        'textarea' => 'Long text',
                        'number' => 'Number',
                        'percent' => 'Percent',
                        'date' => 'Date',
                        'money' => '€uro',
                        'select' => 'Single choice',
                        'checkbox' => 'Multiple choice',
                        'fx' => 'Formule (TODO)',
                        'fxe' => '€uro Formule (TODO)',
                    ),
                    'label' => 'Tipo di campo per la risposta',
                ))
                ->add('options', 'textarea', array(
                    'required' => false,
                    'label' => 'Choices options (one choice for row)',
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
