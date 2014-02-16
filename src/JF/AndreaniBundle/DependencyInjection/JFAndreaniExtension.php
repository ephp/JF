<?php

namespace JF\AndreaniBundle\DependencyInjection;

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
class JFAndreaniExtension extends Extension implements IExtension {

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

        $this->newInstall($install, '\JF\AndreaniBundle\Controller\InstallController', 'indexAction');

        $container->setParameter('jf.install', $install);
    }

    public function setMenu(ContainerBuilder $container) {
        $menu = $container->getParameter('jf.menu');

        if ($container->getParameter('jf.mode') == 'online') {
            $menu['utility']['submenu'][] = array(
                'label' => 'Studio Andreani',
                'route' => 'andreani',
                'show' => array('logged' => false),
                'order' => 10,
            );
            $menu['utility']['submenu'][] = array(
                'label' => 'Studio Andreani',
                'route' => 'studio_andreani',
                'show' => array('out_role' => 'R_EPH'),
                'order' => 10,
            );
        }

        $container->setParameter('jf.menu', $menu);
    }

    public function setPackage(ContainerBuilder $container) {
        $package = $container->getParameter('jf.package');

        $this->newPackage($package, 'jf.andr', 'Andreani', 0, true);

        $container->setParameter('jf.package', $package);
    }

    public function setRoles(ContainerBuilder $container) {
        
    }

    public function setWidgets(ContainerBuilder $container) {
        $widgets = $container->getParameter('jf.widgets');

        $this->newWidget($widgets, 'jf.andr.widget', 'Andreani', true, 'JFAndreaniBundle:Widgets:strumenti');

        $container->setParameter('jf.widgets', $widgets);
    }

    public function setSync(ContainerBuilder $container) {
        
    }

}
