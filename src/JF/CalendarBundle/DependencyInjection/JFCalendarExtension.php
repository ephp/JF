<?php

namespace JF\CalendarBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use JF\CoreBundle\DependencyInjection\Interfaces\IExtension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JFCalendarExtension extends Extension implements IExtension, Interfaces\ITipiEventi {

    use Traits\CalendarExtension;

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

        $this->newInstall($install, '\JF\CalendarBundle\Controller\InstallController', 'indexAction');

        $container->setParameter('jf.install', $install);
    }

    public function setMenu(ContainerBuilder $container) {
        $menu = $container->getParameter('jf.menu');

        $menu['utility']['submenu'][] = array(
            'label' => 'Calendario personale',
            'route' => 'calendario_personale',
            'show' => array(
                'logged' => true
            ),
            'order' => 90,
        );

        $container->setParameter('jf.menu', $menu);
    }

    public function setPackage(ContainerBuilder $container) {
        $package = $container->getParameter('jf.package');

        $this->newPackage($package, 'jf.cal', 'CAL', 0, true);

        $container->setParameter('jf.package', $package);
    }

    public function setRoles(ContainerBuilder $container) {
    }

    public function setWidgets(ContainerBuilder $container) {
    }

    public function setTipoEventi(ContainerBuilder $container) {
        $tipiEvento = array();

        $this->newTipoEvento($tipiEvento, 'JFS', 'JF-System', 'ff0000', false, false, false, true);
        $this->newTipoEvento($tipiEvento, 'JFP', 'JF-System', 'ff0000', false, false, true, true);
        $this->newTipoEvento($tipiEvento, 'APP', 'Appuntamento', '0000aa', true, true, false, true);
        $this->newTipoEvento($tipiEvento, 'PRM', 'Promemoria', '00aa00', true, true, false, true);
        $this->newTipoEvento($tipiEvento, 'SCD', 'Scadenza', 'aa0000', true, true, false, true);
        $this->newTipoEvento($tipiEvento, 'RIC', 'Riunione con cliente', '00aaaa', true, true, false, true);
        $this->newTipoEvento($tipiEvento, 'RIU', 'Riunione ufficio', '00aaaa', true, true, true, true);
        
        $container->setParameter('jf.tipi_evento', $tipiEvento);
    }

}
