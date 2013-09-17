<?php

namespace JF\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JFCoreExtension extends Extension {

    use Traits\CoreExtension;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $menu = $roles = $package = $widgets = array();

        $this->newPackage($package, 'jf.core', 'Core', 0, true);

        $this->newRole($roles, 'R_SUPER', 'SUP', 'Super Amministratore');
        $this->newRole($roles, 'R_ADMIN', 'ADM', 'Amministratore');

        $sub_admin = array();
        
        $menu[] = array(
            'label' => 'Home',
            'route' => 'index',
            'show' => array('always' => true),
            'order' => 1,
            'a' => array('class' => 'blblue'),
        );

        $sub_admin[] = array(
            'label' => 'Licenze',
            'route' => 'index',
            'show' => array('in_role' => array('R_EPH')),
            'order' => 20,
        );
        
        $sub_admin[] = array(
            'label' => 'Stato del sistema',
            'route' => 'index',
            'show' => array('in_role' => array('R_EPH')),
            'order' => 999,
        );
        
        $menu['admin'] = array(
            'label' => 'Amministrazione',
            'submenu' => $sub_admin,
            'show' => array('in_role' => array('R_SUPER', 'R_EPH')),
            'order' => 990,
            'a' => array('class' => 'blred'),
        );

        $container->setParameter('jf.widgets', $widgets);
        $container->setParameter('jf.package', $package);
        $container->setParameter('jf.menu', $menu);
        $container->setParameter('jf.roles', $roles);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

}
