<?php

namespace JF\ACLBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClienteType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nome', null, array('attr' => array('tabindex' => 1)))
            ->add('partitaIva', null, array('attr' => array('tabindex' => 2)))
            ->add('indirizzo', null, array('attr' => array('tabindex' => 3)))
            ->add('citta', null, array('attr' => array('tabindex' => 4)))
            ->add('cap', null, array('attr' => array('tabindex' => 5)))
                
            ->add('telefono', null, array('attr' => array('tabindex' => 6)))
            ->add('cellulare', null, array('attr' => array('tabindex' => 7)))
            ->add('fax', null, array('attr' => array('tabindex' => 8)))
            ->add('email', null, array('attr' => array('tabindex' => 9)))
            ->add('sito', null, array('attr' => array('tabindex' => 10)))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JF\ACLBundle\Entity\Cliente'
        ));
    }

    
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'jf_cliente';
    }
}
