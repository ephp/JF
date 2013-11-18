<?php

namespace Claims\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Gestore controller.
 *
 * @Route("/claims/hospital")
 */
class WidgetsController extends Controller {
    use \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * Lists all Gestore entities.
     *
     * @Route("/cerca", name="wigdet_acl_utenze")
     * @Template()
     */
    public function cercaAction() {
        return array();
    }
    
}
