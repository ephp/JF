<?php

namespace Claims\ContecBundle\DependencyInjection;

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
class ClaimsContecExtension extends Extension implements IExtension {

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

        $this->newInstall($install, '\Claims\ContecBundle\Controller\InstallController', 'indexAction');

        $container->setParameter('jf.install', $install);
    }

    public function setMenu(ContainerBuilder $container) {
        $menu = $container->getParameter('jf.menu');

        if (true) {
            $menu['a_claims']['submenu'][] = array(
                'label' => 'Importazione Contec',
                'route' => 'ravinale_import_manuale',
                'show' => array('in_role' => array('C_ADMIN')),
                'order' => 90,
            );
        } else {
            $menu['admin']['submenu']['claims']['submenu'][] = array(
                'label' => 'Importazione Contec',
                'route' => 'ravinale_import_manuale',
                'show' => array('in_role' => array('C_ADMIN')),
                'order' => 90,
            );
        }

        $container->setParameter('jf.menu', $menu);
    }

    public function setPackage(ContainerBuilder $container) {
        $package = $container->getParameter('jf.package');

        $this->newPackage($package, 'cl.h.contec', 'Claims Contec', 0, true);

        $container->setParameter('jf.package', $package);
    }

    public function setRoles(ContainerBuilder $container) {
        
    }

    public function setWidgets(ContainerBuilder $container) {
        
    }

}
