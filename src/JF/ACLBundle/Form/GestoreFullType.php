<?php

namespace JF\ACLBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GestoreFullType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('usernameCanonical')
            ->add('email')
            ->add('emailCanonical')
            ->add('enabled')
            ->add('salt')
            ->add('password')
            ->add('lastLogin')
            ->add('locked')
            ->add('expired')
            ->add('expiresAt')
            ->add('confirmationToken')
            ->add('passwordRequestedAt')
            ->add('roles')
            ->add('credentialsExpired')
            ->add('credentialsExpireAt')
            ->add('sigla')
            ->add('nome')
            ->add('superadmin')
            ->add('admin')
            ->add('coordinatore')
            ->add('gestore')
            ->add('firstname')
            ->add('lastname')
            ->add('nickname')
            ->add('birthday')
            ->add('gender')
            ->add('locale')
            ->add('facebookId')
            ->add('facebook_access_token')
            ->add('twitter_id')
            ->add('twitter_access_token')
            ->add('email_nuova')
            ->add('email_nuova_token')
            ->add('slug')
            ->add('dati')
            ->add('avatar')
            ->add('google_id')
            ->add('google_access_token')
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
        return 'jf_aclbundle_gestore';
    }
}
