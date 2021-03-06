<?php

namespace SLC\ImportBundle\DependencyInjection;

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
class SLCImportExtension extends Extension implements IExtension {

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

        $this->newInstall($install, '\SLC\ImportBundle\Controller\InstallController', 'indexAction');

        $container->setParameter('jf.install', $install);
    }

    public function setMenu(ContainerBuilder $container) {
        $menu = $container->getParameter('jf.menu');

        if ($container->getParameter('jf.mode') == 'online') {
            if (true) {
                $menu['a_claims']['submenu'][] = array(
                    'label' => 'Importa dati schede mancanti',
                    'route' => 'slc_import_claims',
                    'show' => array('in_role' => array('R_SUPER'), 'license' => array('slc.h.import-import' => array('slc'))),
                    'order' => 999,
                );

                $menu['a_claims']['submenu'][] = array(
                    'label' => 'Importa dati tutte schede',
                    'route' => 'slc_import_all_claims',
                    'show' => array('in_role' => array('R_SUPER'), 'license' => array('slc.h.import-import' => array('slc'))),
                    'order' => 1000,
                );
            } else {
                $menu['admin']['submenu']['claims']['submenu'][] = array(
                    'label' => 'Importa dati JF-Claims',
                    'route' => 'slc_import_claims',
                    'show' => array('in_role' => array('C_ADMIN'), 'license' => array('slc.h.import-import' => 'slc')),
                    'order' => 999,
                );
            }
        }

        $container->setParameter('jf.menu', $menu);
    }

    public function setPackage(ContainerBuilder $container) {
        $package = $container->getParameter('jf.package');

        $this->newPackage($package, 'slc.h.import', 'Import Claims Hospital', 0, true);

        $container->setParameter('jf.package', $package);
    }

    public function setRoles(ContainerBuilder $container) {
        
    }

    public function setWidgets(ContainerBuilder $container) {
        
    }

    public function setSync(ContainerBuilder $container) {
        
    }

}
