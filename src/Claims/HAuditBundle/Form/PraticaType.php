<?php

namespace Claims\HAuditBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PraticaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sre', null, array('label' => 'SRE'))
            ->add('mfRef', null, array('label' => 'MF Ref.'))
            ->add('gruppo', null, array('label' => 'TPA'))
            ->add('dol', null, array('label' => 'DOL', 'widget' => 'single_text', 'format' => 'dd-MM-yyyy'))
            ->add('don', null, array('label' => 'DON', 'widget' => 'single_text', 'format' => 'dd-MM-yyyy'))
            ->add('dsCode', null, array('label' => 'DS Code'))
            ->add('status', null, array('label' => 'Status'))
            ->add('reserve', null, array('label' => 'Amount Reserve'))
            ->add('totalPaid', null, array('label' => 'Total Paid'))
            ->add('proReserve', null, array('label' => 'Pro Reserve'))
            ->add('indemnityCtpPaid', null, array('label' => 'Indemnity CTP Paid'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Claims\HAuditBundle\Entity\Pratica'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'claims_hauditbundle_pratica';
    }
}
