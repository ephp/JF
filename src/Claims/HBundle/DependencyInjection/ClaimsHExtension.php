<?php

namespace Claims\HBundle\DependencyInjection;

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
class ClaimsHExtension extends Extension implements IExtension {

    use CoreExtension;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->configure($container);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }

    public function setInstall(ContainerBuilder $container) {
        $install = $container->getParameter('jf.install');

        $this->newInstall($install, '\Claims\HBundle\Controller\InstallController', 'indexAction');

        $container->setParameter('jf.install', $install);
    }

    public function setMenu(ContainerBuilder $container) {
        $menu = $container->getParameter('jf.menu');

        if (true) {

            $menu['a_claims']['submenu'][] = array(
                'label' => 'Sistemi',
                'route' => 'eph_claims_h_sistemi',
                'show' => array('in_role' => array('R_EPH')),
                'order' => 10,
            );
            
            $menu['claims']['submenu'][] = array(
                'label' => 'Hospital',
                'route' => 'claims_hospital',
                'show' => array('in_role' => array('C_ADMIN', 'C_GESTORE_H')),
                'order' => 10,
            );
            
            $menu['claims']['submenu'][] = array(
                'label' => 'Stati pratiche Hospital',
                'route' => 'claims_stati_hospital',
                'show' => array('in_role' => array('C_ADMIN', 'C_GESTORE_H')),
                'order' => 20,
            );

        } else {

            $menu['admin']['submenu']['claims']['submenu'][] = array(
                'label' => 'Sistemi',
                'route' => 'eph_claims_h_sistemi',
                'show' => array('in_role' => array('R_EPH')),
                'order' => 10,
            );

        }

        $container->setParameter('jf.menu', $menu);
    }

    public function setPackage(ContainerBuilder $container) {
        $package = $container->getParameter('jf.package');

        $this->newPackage($package, 'cl.h', 'Claims Hospital', 0, true);

        $container->setParameter('jf.package', $package);
    }

    public function setRoles(ContainerBuilder $container) {
        $roles = $container->getParameter('jf.roles');

        $this->newRole($roles, 'C_GESTORE_H', 'H-GEST', 'Gestore Claims Hospital');
        $this->newRole($roles, 'C_RECUPERI_H', 'H-REC', 'Addetto ai recuperi Claims Hospital');

        $container->setParameter('jf.roles', $roles);
    }

    public function setWidgets(ContainerBuilder $container) {
        
    }

}
