<?php

namespace JF\CoreBundle\DependencyInjection;

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
class JFCoreExtension extends Extension implements IExtension {

    use CoreExtension;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->configure($container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
    
    public function setInstall(ContainerBuilder $container) {
        $install = array();
        
        $this->newInstall($install, '\JF\CoreBundle\Controller\InstallController', 'indexAction');
        
        $container->setParameter('jf.install', $install);
    }

    public function setMenu(ContainerBuilder $container) {
        $menu = array();
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
            'a' => array('class' => 'todo'),
        );
        
        $sub_admin[] = array(
            'label' => 'Stato del sistema',
            'route' => 'index',
            'show' => array('in_role' => array('R_EPH')),
            'order' => 999,
            'a' => array('class' => 'todo'),
        );
        
        $menu['admin'] = array(
            'label' => 'Amministrazione',
            'submenu' => $sub_admin,
            'show' => array('in_role' => array('R_SUPER', 'R_EPH')),
            'order' => 990,
            'a' => array('class' => 'blred'),
        );
        
        $container->setParameter('jf.menu', $menu);
    }

    public function setPackage(ContainerBuilder $container) {
        $package = array();
        
        $this->newPackage($package, 'jf.core', 'Core', 0, true);
        
        $container->setParameter('jf.package', $package);
    }
    
    public function setRoles(ContainerBuilder $container) {
        $roles = array();
               
        $container->setParameter('jf.roles', $roles);
    }

    public function setWidgets(ContainerBuilder $container) {
        $widgets = array();
        
        $container->setParameter('jf.widgets', $widgets);
    }

}
