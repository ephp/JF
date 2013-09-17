<?php

namespace JF\ACLBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Gestore controller.
 *
 * @Route("/rubrica")
 */
class RubricaController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \JF\CoreBundle\Controller\Traits\CoreController,
        \Ephp\ACLBundle\Controller\Traits\NotifyController;

    /**
     * Lists all Gestore entities.
     *
     * @Route("/", name="rubrica", options={"ACL": {"out_role": "R_EPH"}})
     * @Template()
     */
    public function indexAction() {
        $cliente = $this->getUser()->getCliente();
        $entities = $this->findBy('JFACLBundle:Gestore', array('cliente' => $cliente->getId(), 'locked' => false));
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all Gestore entities.
     *
     * @Route("-help", name="help_rubrica", options={"expose": true,"ACL": {"out_role": "R_EPH"}})
     * @Template()
     */
    public function helpIndexAction() {
        return array();
    }


}
