<?php

namespace JF\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/calendario")
 */
class DefaultController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        Traits\CalendarController;

    /**
     * @Route("/{mese}-{anno}", name="calendario_personale", defaults={"mese": null, "anno": null})
     * @Template()
     */
    public function indexAction($mese, $anno) {
        if(!$mese) {
            $mese = date('m');
        }
        if(!$anno) {
            $anno = date('Y');
        }
//        $eventi = $this->getRepository('JFCalendarBundle:Evento')->calendarioMese($this->getUser(), $mese, $anno);
        $eventi = $this->getRepository('ClaimsHBundle:Evento')->calendarioMese($this->getUser(), $mese, $anno);
        
        $giorni = array();
        foreach($eventi as $evento) {
            /* @var $evento \Claims\HBundle\Entity\Evento */
            if(!isset($giorni[$evento->getDataOra()->format('d')])) {
                $giorni[$evento->getDataOra()->format('d')] = 0;
            }
            $giorni[$evento->getDataOra()->format('d')]++;
        }
        
        return array(
            'mese' => $mese,
            'anno' => $anno,
            'eventi' => $eventi,
            'giorni' => $giorni,
        );
    }

}
