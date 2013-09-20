<?php

namespace JF\AndreaniBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Gestore controller.
 *
 * @Route("/andreani/widgets")
 */
class WidgetsController extends Controller {
    use \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * Lists all Gestore entities.
     *
     * @Route("/utenti", name="wigdet_andreani_strumenti")
     * @Template()
     */
    public function strumentiAction() {
        return array();
    }
    
}
