<?php

namespace JF\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/render-jf-calendar")
 * @Template()
 */
class RenderController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \JF\CalendarBundle\Controller\Traits\CalendarController;

    /**
     * @Route("/evento", name="render_jf_calendar_evento")
     * @Template()
     */
    public function nuovoEventoAction() {
        return array(
            'tipi' => $this->getUser()->getCliente()->getTipiEventiPrivati($this->container->getParameter('jf.tipi_evento', array())),
        );
    }

}