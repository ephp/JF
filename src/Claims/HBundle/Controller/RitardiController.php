<?php

namespace Claims\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ephp\UtilityBundle\Utility\Debug;

/**
 * @Route("/h/ritardi")
 */
class RitardiController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController;

    /**
     * Lists all Gestore entities.
     *
     * @Route("/",      name="claims_h_ritardi",       defaults={"mode": "personale"}, options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("/tutti", name="claims_h_ritardi_tutti", defaults={"mode": "completo"},  options={"ACL": {"in_role": {"C_ADMIN", "C_RECUPERI_H"}}})
     * @Template()
     */
    public function indexAction($mode) {
        $gestore = $this->getUser();
        $cliente = $gestore->getCliente();
        if ($this->getParam('_route') == 'claims_h_ritardi_tutti' || !$gestore->hasRole('C_GESTORE_H')) {
            $ritardi = $this->getRepository('ClaimsHBundle:Pratica')->ritardi($cliente->getId());
        } else {
            $ritardi = $this->getRepository('ClaimsHBundle:Pratica')->ritardi($cliente->getId(), $gestore->getId());
        }
        $pagination = $this->createPagination($ritardi, 50);
        return array(
            'pagination' => $pagination,
            'show_gestore' => true,
            'links' => $this->buildLinks(),
            'mode' => $mode,
        );
    }

    private function buildLinks() {
        $out = array();
        if ($this->getUser()->hasRole(array('C_GESTORE_H'))) {
            $out['personale'] = array(
                'route' => 'claims_hospital_personale',
                'label' => 'Personale'
            );
        }
        if ($this->getUser()->hasRole(array('C_ADMIN', 'C_RECUPERI_H'))) {
            $out['completo'] = array(
                'route' => 'claims_stati_hospital_completo',
                'label' => 'Completo'
            );
        }
        return $out;
    }

}

