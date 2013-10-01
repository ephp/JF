<?php

namespace Claims\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/render-claims-hospital")
 * @Template()
 */
class RenderController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * @Route("/priorita", name="render_claims_hospital_priorita")
     * @Template()
     */
    public function cambiaPrioritaAction() {
        $priorita = $this->findBy('ClaimsCoreBundle:Priorita', array('show' => true));
        return array(
            'priorita' => $priorita,
        );
    }

    /**
     * @Route("/dasc", name="render_claims_hospital_dasc")
     * @Template()
     */
    public function cambiaDascAction() {
        return array(
        );
    }

    /**
     * @Route("/gestore", name="render_claims_hospital_gestori")
     * @Template()
     */
    public function cambiaGestoreAction() {
        $gestori = $this->findBy('JFACLBundle:Gestore', array('cliente' => $this->getUser()->getCliente()->getId()));
        return array(
            'gestori' => $gestori,
        );
    }

    /**
     * @Route("/note", name="render_claims_hospital_note")
     * @Template()
     */
    public function cambiaNoteAction() {
        return array(
        );
    }

    /**
     * @Route("/stato", name="render_claims_hospital_stato")
     * @Template()
     */
    public function cambiaStatoAction() {
        $stati = $this->findBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $this->getUser()->getCliente()->getId()));
        return array(
            'stati' => $stati,
        );
    }
    
    /**
     * @Route("/dettagli", name="render_claims_hospital_dettagli")
     * @Template()
     */
    public function dettagliAction() {
        return array(
        );
    }
    
    /**
     * @Route("/evento/{id}", name="render_claims_hospital_evento")
     * @Template()
     */
    public function nuovoEventoAction($id) {
        return array(
            'entity' => $this->find('ClaimsHBundle:Pratica', $id),
        );
    }
    
    /**
     * @Route("/link/{id}", name="render_claims_hospital_link")
     * @Template()
     */
    public function nuovoLinkAction($id) {
        return array(
            'entity' => $this->find('ClaimsHBundle:Pratica', $id),
        );
    }

    
}