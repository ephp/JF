<?php

namespace Claims\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StatoPraticaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stato')
            ->add('tab', null, array('label' => 'Visualizza nella pagina degli stati'))
            ->add('stats', null, array('label' => 'Visualizza nelle statistiche'))
            ->add('primo', null, array('label' => 'Stato di default appena importata la pratica'))
            ->add('annota', null, array('label' => 'Memorizza su calendario quando il cambio di stato'))
            ->add('chiudi', null, array('label' => 'Chiude la pratica quando selezionato'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Claims\CoreBundle\Entity\StatoPratica'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'claims_corebundle_statopratica';
    }
}
