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
                ->add('question', null, array(
                    'label' => 'Domanda',
                ))
                ->add('example', null, array(
                    'label' => 'Esempio risposta',
                ))
                ->add('type', 'choice', array(
                    'choices' => array(
                        'textarea' => 'Testo libero',
                        'number' => 'Numero',
                        'date' => 'Data',
                        'money' => 'Importo (€)',
                        'select' => 'Menu a tendina',
                        'checkbox' => 'Scelta multipla',
                    ),
                    'label' => 'Tipo ri capo per la risposta',
                ))
                ->add('options', 'textarea', array(
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
