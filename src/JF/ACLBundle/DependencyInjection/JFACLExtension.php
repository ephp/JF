<?php

namespace JF\ACLBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use JF\CoreBundle\DependencyInjection\Interfaces\IExtension;
use JF\CoreBundle\DependencyInjection\Traits\CoreExtension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JFACLExtension extends Extension implements IExtension {

    use CoreExtension;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->configure($container);

        $loaderYml = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loaderYml->load('services.yml');
        $loaderXml = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loaderXml->load('services.xml');
    }

    public function setInstall(ContainerBuilder $container) {
        $install = $container->getParameter('jf.install');

        $this->newInstall($install, '\JF\ACLBundle\Controller\InstallController', 'indexAction');

        $container->setParameter('jf.install', $install);
    }

    public function setMenu(ContainerBuilder $container) {
        $menu = $container->getParameter('jf.menu');

        $menu['admin']['submenu'][] = array(
            'label' => 'Utenze',
            'route' => 'utenze',
            'show' => array(
                'in_role' => array('R_SUPER'),
                'license' => array('jf.acl-utenze' => array('small', 'medium', 'big', 'unlimited', 'slc'))
            ),
            'order' => 10,
        );
        $menu['admin']['submenu'][] = array(
            'label' => 'Clienti',
            'route' => 'eph_clienti',
            'show' => array('in_role' => array('R_EPH')),
            'order' => 1,
        );
        
        $menu['login'] = array(
            'label' => 'Login',
            'route' => 'fos_user_security_login',
            'show' => array('logged' => false),
            'order' => 999,
            'a' => array('class' => 'blyellow'),
            'icon' => 'ico-lock',
        );

        $menu['profilo'] = array(
            'label' => 'Profilo',
            'submenu' => array(),
            'show' => array('logged' => true),
            'order' => 999,
            'a' => array('class' => 'blyellow'),
            'icon' => 'ico-user',
        );
        $menu['profilo']['submenu'][] = array(
            'label' => 'Visualizza',
            'route' => 'fos_user_profile_show',
            'order' => 1,
        );

        $menu['profilo']['submenu'][] = array(
            'label' => 'Cambia Password',
            'route' => 'fos_user_change_password',
            'order' => 25,
        );

        $menu['profilo']['submenu'][] = array(
            'label' => 'Logout',
            'route' => '_security_logout',
            'order' => 99,
        );


        $menu['utility']['submenu'][] = array(
            'label' => 'Rubrica',
            'route' => 'rubrica',
            'show' => array(
                'out_role' => 'R_EPH',
                'license' => array('jf.acl-utenze' => array('small', 'medium', 'big', 'unlimited', 'slc'))
            ),
            'order' => 90,
        );

        $container->setParameter('jf.menu', $menu);
    }

    public function setPackage(ContainerBuilder $container) {
        $package = $container->getParameter('jf.package');

        $this->newPackage($package, 'jf.acl', 'ACL', 0, true);

        $container->setParameter('jf.package', $package);
    }

    public function setRoles(ContainerBuilder $container) {
        $roles = $container->getParameter('jf.roles');

        $this->newRole($roles, 'R_ADMIN', 'ADM', 'Amministratore');

        $container->setParameter('jf.roles', $roles);
    }

    public function setWidgets(ContainerBuilder $container) {
        $widgets = $container->getParameter('jf.widgets');

        $this->newWidget($widgets, 'jf.acl.locked', 'ACLock', array('R_SUPER'), 'JFACLBundle:Widgets:lock');
        $this->newWidget($widgets, 'jf.acl.utenti', 'Utenti', array('R_SUPER'), 'JFACLBundle:Widgets:utenti');
        $this->newWidget($widgets, 'jf.acl.licenze', 'Licenze', array('R_SUPER'), 'JFACLBundle:Widgets:licenze');

        $container->setParameter('jf.widgets', $widgets);
    }

}
