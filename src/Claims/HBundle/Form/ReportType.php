<?php

namespace Claims\HBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReportType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('number', 'hidden')
                ->add('copertura', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('stato', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('descrizione_in_fatto', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('relazione_avversaria', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('relazione_ex_adverso', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('medico_legale1', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('medico_legale2', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('medico_legale3', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('valutazione_responsabilita', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('analisi_danno', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('riserva', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('possibile_rivalsa', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('azioni', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('richiesta_sa', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
                ->add('note', null, array('attr' => array('class' => 'autoupdate autogrow timing', 'report' => 'report_number')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Claims\HBundle\Entity\Report'
        ));
    }

    public function getName() {
        return 'report';
    }

}
