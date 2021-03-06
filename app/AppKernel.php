<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new PunkAve\FileUploaderBundle\PunkAveFileUploaderBundle(),
            
            new Ephp\UtilityBundle\EphpUtilityBundle(),
            new Ephp\CalendarBundle\EphpCalendarBundle(),
            new Ephp\DragDropBundle\EphpDragDropBundle(),
            new Ephp\ACLBundle\EphpACLBundle(),
            new Ephp\ImapBundle\EphpImapBundle(),
            new Ephp\OfficeBundle\EphpOfficeBundle(),
            
            new JF\GeneratorBundle\JFGeneratorBundle(),
            new JF\CoreBundle\JFCoreBundle(),
            new JF\CalendarBundle\JFCalendarBundle(),
            new JF\ACLBundle\JFACLBundle(),
            new JF\AndreaniBundle\JFAndreaniBundle(),
            new JF\DragDropBundle\JFDragDropBundle(),
            
            new Claims\CoreBundle\ClaimsCoreBundle(),
            new Claims\HBundle\ClaimsHBundle(),
            new Claims\ContecBundle\ClaimsContecBundle(),
            new Claims\RavinaleBundle\ClaimsRavinaleBundle(),
            
            new SLC\ImportBundle\SLCImportBundle(),
            new SLC\HBundle\SLCHBundle(),
            new JF\GitHubBundle\JFGitHubBundle(),
            new Claims\HAuditBundle\ClaimsHAuditBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
