<?php

namespace JF\ACLBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JFACLExtension extends Extension {

    use \JF\CoreBundle\DependencyInjection\Traits\CoreExtension;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $widgets = $container->getParameter('jf.widgets');
        $package = $container->getParameter('jf.package');
        $menu = $container->getParameter('jf.menu');
        $roles = $container->getParameter('jf.roles');

        $this->newPackage($package, 'jf.acl', 'ACL', 0, true);
        $this->newWidget($widgets, 'jf.acl.locked', 'ACLock', array('R_SUPER'), 'JFACLBundle:Widgets:lock');
        $this->newWidget($widgets, 'jf.acl.utenti', 'Utenti', array('R_SUPER'), 'JFACLBundle:Widgets:utenti');

        $sub_admin = $sub_prof = $sub_util = array();
        $sub_admin[] = array(
            'label' => 'Utenze',
            'route' => 'utenze',
            'show' => array('in_role' => array('R_SUPER')),
            'order' => 10,
        );
        $sub_admin[] = array(
            'label' => 'Clienti',
            'route' => 'eph_clienti',
            'show' => array('in_role' => array('R_EPH')),
            'order' => 1,
        );
        $menu['admin']['submenu'] = array_merge($menu['admin']['submenu'], $sub_admin);

        $menu[] = array(
            'label' => 'Login',
            'route' => 'fos_user_security_login',
            'show' => array('logged' => false),
            'order' => 999,
            'a' => array('class' => 'blyellow'),
        );
        
        $sub_prof[] = array(
            'label' => 'Logout',
            'route' => '_security_logout',
            'order' => 99,
        );
        
        $sub_prof[] = array(
            'label' => 'Visualizza',
            'route' => 'fos_user_profile_show',
            'order' => 1,
        );
        
        $sub_prof[] = array(
            'label' => 'Cambia Password',
            'route' => 'fos_user_change_password',
            'order' => 25,
        );
        
        $menu[] = array(
            'label' => 'Profilo',
            'submenu' => $sub_prof,
            'show' => array('logged' => true),
            'order' => 999,
            'a' => array('class' => 'blyellow'),
        );
        
        $sub_util[] = array(
            'label' => 'Rubrica',
            'route' => 'rubrica',
            'show' => array('out_role' => 'R_EPH'),
            'order' => 90,
        );
        
        $menu[] = array(
            'label' => 'Utility',
            'submenu' => $sub_util,
            'order' => 100,
            'a' => array('class' => 'blgreen'),
        );

        $container->setParameter('jf.widgets', $widgets);
        $container->setParameter('jf.package', $package);
        $container->setParameter('jf.menu', $menu);
        $container->setParameter('jf.roles', $roles);

        $loaderYml = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loaderYml->load('services.yml');
        $loaderXml = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loaderXml->load('services.xml');
    }

}
