<?php

namespace Claims\HBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RicercaType extends AbstractType {

    private $mode;

    function __construct($mode) {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $p = new \Claims\HBundle\Entity\Pratica();
        $builder
                ->add('anno', 'choice', array('required' => false, 'empty_value' => 'Tutti', 'choices' => array_combine(range(7, date('y')), range(2007, date('Y'))), 'attr' => array('style' => 'max-width: 400px')))
                ->add('codice', null, array('required' => false, 'label' => 'TPA', 'attr' => array('style' => 'max-width: 400px')))
                ->add('claimant', null, array('required' => false, 'attr' => array('style' => 'max-width: 400px')))
                ->add('soi', 'choice', array('required' => false, 'empty_value' => 'Tutti', 'choices' => $p->severityOfInjury(), 'attr' => array('style' => 'max-width: 400px')))
                ->add('amountReserved', 'choice', array('required' => false, 'empty_value' => 'Tutti', 'choices' => array('N.P.' => 'N.P.', 'Non N.P.' => 'Non N.P.'), 'attr' => array('style' => 'max-width: 400px')))
                ->add('ospedale', null, array('required' => false, 'empty_value' => 'Tutti', 'attr' => array('style' => 'max-width: 400px')))
                ->add('court', 'choice', array('required' => false, 'label' => 'Giudiziale', 'empty_value' => 'Tutti', 'choices' => array('Sì' => 'Giudiziale', 'No' => 'Non Giudiziale'), 'attr' => array('style' => 'max-width: 400px')))
                ->add('status', null, array('required' => false, 'attr' => array('style' => 'max-width: 400px')))
                ->add('statoPratica', null, array('required' => false, 'empty_value' => 'Tutti', 'attr' => array('style' => 'max-width: 400px')))
                ->add('priorita', null, array('required' => false, 'empty_value' => 'Tutte', 'attr' => array('style' => 'max-width: 400px')))
        ;
        if (in_array($this->mode, array('completo', 'senza_dasc', 'senza_gestore', 'chiusi'))) {
            $builder
                    ->add('gestore', null, array('required' => false, 'empty_value' => 'Tutti', 'attr' => array('style' => 'max-width: 400px')))
            ;
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Claims\HBundle\Entity\Pratica'
        ));
    }

    public function getName() {
        return 'ricerca';
    }

}
