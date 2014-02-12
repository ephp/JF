<?php

namespace JF\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use JF\CoreBundle\Controller\Traits\InstallController as BaseInstall;

/**
 * @Route("/__sync")
 */
class SyncController extends Controller {

    use BaseController;

    /**
     * @Route("-acl/fetch", name="sync_acl_fetch"), defaults={"_format": "json"})
     * @Method("POST")
     */
    public function fetchAction() {
    }

    /**
     * @Route("-acl/push", name="sync_acl_push"), defaults={"_format": "json"})
     */
    public function pushAction() {
        $out = array(
            'package' => array(),
            'groups' => array(),
            'license' => array(),
        );
        $packages = $this->findAll('JFCoreBundle:Package');
        foreach($packages as $package) {
            /* @var $package \JF\CoreBundle\Entity\Package */
            $out['package'][$package->getId()] = array(
                'nome' => $package->getNome(),
                'sigla' => $package->getSigla(),
                'descrizione' => $package->getDescrizione(),
            );
        }
        $gruppi = $this->findAll('JFCoreBundle:Gruppo');
        foreach($gruppi as $gruppo) {
            /* @var $gruppo \JF\CoreBundle\Entity\Gruppo */
            $out['groups'][$gruppo->getId()] = array(
                'nome' => $gruppo->getNome(),
                'sigla' => $gruppo->getSigla(),
                'descrizione' => $gruppo->getDescrizione(),
                'package' => $gruppo->getPackage()->getId(),
            );
        }
        $licenze = $this->findAll('JFCoreBundle:Licenza');
        foreach($licenze as $licenza) {
            /* @var $licenza \JF\CoreBundle\Entity\Licenza */
            $out['license'][$licenza->getId()] = array(
                'nome' => $licenza->getNome(),
                'ordine' => $licenza->getOrdine(),
                'autoinstall' => $licenza->getAutoinstall(),
                'descrizione' => $licenza->getDescrizione(),
                'durata' => $licenza->getDurata(),
                'gruppo' => $licenza->getGruppo()->getId(),
                'market' => $licenza->getMarket(),
                'params' => $licenza->getParams(),
                'prezzo' => $licenza->getPrezzo(),
                'roles' => $licenza->getRoles(),
                'sconto' => $licenza->getSconto(),
                'route' => $licenza->getRoute(),
                'sigla' => $licenza->getSigla(),
                'stato' => $licenza->getStato(),
                'widgets' => $licenza->getWidgets(),
            );
        }
        return $this->jsonResponse($out);
    }

}
