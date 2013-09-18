<?php

namespace JF\ACLBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Gestore controller.
 *
 * @Route("/acl/widgets")
 */
class WidgetsController extends Controller {
    use \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * Lists all Gestore entities.
     *
     * @Route("/utenti", name="wigdet_acl_utenze")
     * @Template()
     */
    public function utentiAction() {
        $entity = $this->getUser()->getCliente();
        return array('entity' => $entity);
    }
    
    /**
     * Lists all Gestore entities.
     *
     * @Route("/licenze", name="wigdet_acl_licenze")
     * @Template()
     */
    public function licenzeAction() {
        $entity = $this->getUser()->getCliente();
        return array('entity' => $entity);
    }
    
    /**
     * Lists all Gestore entities.
     *
     * @Route("/lock", name="wigdet_acl_lock")
     * @Template()
     */
    public function lockAction() {
        $entity = $this->getUser()->getCliente();
        return array('entity' => $entity);
    }
}
