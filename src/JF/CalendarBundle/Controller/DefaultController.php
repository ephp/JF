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
        $eventi = array();
        foreach($this->getUser()->getCliente()->get('calendario_personale') as $cal) {
            $eventi[$cal['label']] = array(
                'eventi' => $this->getRepository($cal['entity'])->calendarioMese($this->getUser(), $mese, $anno),
                'css' => $cal['css'],
            );
        }
        
        $giorni = array();
        foreach($eventi as $label => $cal) {
            foreach($cal['eventi'] as $evento) {
                /* @var $evento \Claims\HBundle\Entity\Evento */
                if(!isset($giorni[$evento->getDataOra()->format('d')])) {
                    $giorni[$evento->getDataOra()->format('d')] = array(
                        'tot' => 0,
                        'tipo' => array(),
                    );
                }
                if(!isset($giorni[$evento->getDataOra()->format('d')]['tipo'][$label])) {
                    $giorni[$evento->getDataOra()->format('d')]['tipo'][$label] = array(
                        'n' => 0,
                        'css' => $cal['css'],
                    );
                }
                $giorni[$evento->getDataOra()->format('d')]['tipo'][$label]['n']++;
                $giorni[$evento->getDataOra()->format('d')]['tot']++;
            }
        }
        
        return array(
            'mese' => $mese,
            'anno' => $anno,
            'eventi' => $eventi,
            'giorni' => json_encode($giorni),
        );
    }

}
