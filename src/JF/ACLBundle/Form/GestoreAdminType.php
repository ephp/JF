<?php

namespace JF\ACLBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GestoreAdminType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sigla', null, array('attr' => array('tabindex' => 1)))
            ->add('firstname', null, array('label' => 'Nome', 'attr' => array('tabindex' => 3)))
            ->add('lastname', null, array('label' => 'Cognome', 'attr' => array('tabindex' => 4)))
            ->add('email', null, array('attr' => array('tabindex' => 2)))
            ->add('telefono', null, array('attr' => array('tabindex' => 5)))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JF\ACLBundle\Entity\Gestore'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jf_gestore';
    }
}
