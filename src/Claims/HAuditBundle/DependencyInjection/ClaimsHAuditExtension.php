<?php

namespace Claims\HAuditBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use JF\CoreBundle\DependencyInjection\Interfaces\IExtension;
use JF\CalendarBundle\DependencyInjection\Traits\CalendarExtension;
use JF\CalendarBundle\DependencyInjection\Interfaces\ITipiEventi;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ClaimsHAuditExtension extends Extension implements IExtension, ITipiEventi {

    use CalendarExtension;

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

        $this->newInstall($install, '\Claims\HAuditBundle\Controller\InstallController', 'indexAction');

        $container->setParameter('jf.install', $install);
    }

    public function setMenu(ContainerBuilder $container) {
        $menu = $container->getParameter('jf.menu');

        $menu['claims_audit'] = array(
            'label' => 'Claims Audit',
            'submenu' => array(),
            'order' => 10,
            'a' => array('class' => 'blred'),
            'icon' => 'ico-eye-open',
        );

        $menu['claims_audit']['submenu'][] = array(
            'label' => 'Audit Hospital',
            'route' => 'claims-h-audit',
            'show' => array('in_role' => array('C_AUDIT_H')),
            'order' => 50,
        );

        $menu['claims_audit']['submenu'][] = array(
            'label' => 'New Audit Hospital',
            'route' => 'claims-h-audit_new',
            'show' => array('in_role' => array('C_AUDIT_H')),
            'order' => 10,
        );

        $container->setParameter('jf.menu', $menu);
    }

    public function setPackage(ContainerBuilder $container) {
        $package = $container->getParameter('jf.package');

        $this->newPackage($package, 'cl.h.audit', 'Claims Hospital Audit', 0, true);

        $container->setParameter('jf.package', $package);
    }

    public function setRoles(ContainerBuilder $container) {
        $roles = $container->getParameter('jf.roles');

        $this->newRole($roles, 'C_AUDIT_H', 'C-AUD', 'Audit');

        $container->setParameter('jf.roles', $roles);
    }

    public function setTipoEventi(ContainerBuilder $container) {
        $tipiEvento = $container->getParameter('jf.tipi_evento');
        
        $this->addLicenzaTipoEvento($tipiEvento, 'AUD', 'cl.h-audit');
        
        $container->setParameter('jf.tipi_evento', $tipiEvento);
    }

    public function setWidgets(ContainerBuilder $container) {
        
    }

}
