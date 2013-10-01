<?php

namespace Claims\CoreBundle\DependencyInjection;

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
class ClaimsCoreExtension extends Extension implements IExtension {

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

        $this->newInstall($install, '\Claims\CoreBundle\Controller\InstallController', 'indexAction');

        $container->setParameter('jf.install', $install);
    }

    public function setMenu(ContainerBuilder $container) {
        $menu = $container->getParameter('jf.menu');

        $menu['claims'] = array(
            'label' => 'Claims',
            'submenu' => array(),
            'order' => 10,
            'a' => array('class' => 'blblue'),
        );
        
        $menu['a_claims'] = array(
            'label' => 'Gestione Claims',
            'submenu' => array(),
            'show' => array('in_role' => array('C_ADMIN', 'R_EPH')),
            'order' => 110,
            'a' => array('class' => 'bldblue'),
        );

        if (true) {

            $menu['a_claims']['submenu'][] = array(
                'label' => 'Priorita',
                'route' => 'eph_priorita',
                'show' => array('in_role' => array('R_EPH')),
                'order' => 10,
            );

            $menu['a_claims']['submenu'][] = array(
                'label' => 'Stato delle pratiche',
                'route' => 'claims_stato_pratica',
                'show' => array('in_role' => array('C_ADMIN')),
                'order' => 10,
            );

        } else {

            $menu['admin']['submenu']['claims'] = array(
                'label' => 'Claims',
                'submenu' => array(),
                'show' => array('in_role' => array('R_EPH', 'C_ADMIN')),
                'order' => 100,
            );

            $menu['admin']['submenu']['claims']['submenu'][] = array(
                'label' => 'Priorita',
                'route' => 'eph_priorita',
                'show' => array('in_role' => array('R_EPH')),
                'order' => 10,
            );

            $menu['admin']['submenu']['claims']['submenu'][] = array(
                'label' => 'Stato delle pratiche',
                'route' => 'claims_stato_pratica',
                'show' => array('in_role' => array('C_ADMIN')),
                'order' => 10,
            );
        }

        $container->setParameter('jf.menu', $menu);
    }

    public function setPackage(ContainerBuilder $container) {
        $package = $container->getParameter('jf.package');

        $this->newPackage($package, 'cl.core', 'Claims', 0, true);

        $container->setParameter('jf.package', $package);
    }

    public function setRoles(ContainerBuilder $container) {
        $roles = $container->getParameter('jf.roles');

        $this->newRole($roles, 'C_ADMIN', 'C-ADM', 'Amministratore Claims');
        $this->newRole($roles, 'C_GESTORE', 'C-GEST', 'Gestore Claims');

        $container->setParameter('jf.roles', $roles);
    }

    public function setWidgets(ContainerBuilder $container) {
        
    }

}
