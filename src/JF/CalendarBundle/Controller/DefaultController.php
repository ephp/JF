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
     * @Route("-update/{mese}-{anno}", name="calendario_personale_json", defaults={"_format": "json", "mese": null, "anno": null}, options={"expose": true})
     * @Template()
     */
    public function indexAction($mese, $anno) {
        if (!$mese) {
            $mese = date('m');
        }
        if (!$anno) {
            $anno = date('Y');
        }
//        $eventi = $this->getRepository('JFCalendarBundle:Evento')->calendarioMese($this->getUser(), $mese, $anno);
        $eventi = array();
        foreach ($this->getUser()->getCliente()->get('calendario_personale') as $cal) {
            $eventi[$cal['label']] = array(
                'eventi' => $this->getRepository($cal['entity'])->calendarioMese($this->getUser(), $mese, $anno),
                'css' => $cal['css'],
            );
        }

        $giorni = array();
        foreach ($eventi as $label => $cal) {
            foreach ($cal['eventi'] as $evento) {
                /* @var $evento \Claims\HBundle\Entity\Evento */
                if (!isset($giorni[$evento->getDataOra()->format('d')])) {
                    $giorni[$evento->getDataOra()->format('d')] = array(
                        'tot' => 0,
                        'tipo' => array(),
                    );
                }
                if (!isset($giorni[$evento->getDataOra()->format('d')]['tipo'][$label])) {
                    $giorni[$evento->getDataOra()->format('d')]['tipo'][$label] = array(
                        'n' => 0,
                        'css' => $cal['css'],
                    );
                }
                $giorni[$evento->getDataOra()->format('d')]['tipo'][$label]['n'] ++;
                $giorni[$evento->getDataOra()->format('d')]['tot'] ++;
            }
        }

        switch ($this->getParam('_route')) {
            case 'calendario_personale_json':
                return $this->jsonResponse($giorni);
            default:
                $giorno = date('d');
                $eventi = array();
                foreach ($this->getUser()->getCliente()->get('calendario_personale') as $cal) {
                    $eventi[$cal['label']] = array(
                        'eventi' => $this->getRepository($cal['entity'])->calendarioMese($this->getUser(), $mese, $anno, $giorno),
                        'css' => $cal['css'],
                    );
                }
                return array(
                    'mese' => $mese,
                    'anno' => $anno,
                    'eventi' => $eventi,
                    'giorni' => json_encode($giorni),
                );
        }
    }

    /**
     * @Route("-eventi/{giorno}-{mese}-{anno}", name="calendario_personale_giorno", defaults={"giorno": null, "mese": null, "anno": null}, options={"expose": true})
     * @Template()
     */
    public function eventiGiornoAction($giorno, $mese, $anno) {
        if (!$giorno) {
            $giorno = date('d');
        }
        if (!$mese) {
            $mese = date('m');
        }
        if (!$anno) {
            $anno = date('Y');
        }
        $eventi = array();
        foreach ($this->getUser()->getCliente()->get('calendario_personale') as $cal) {
            $eventi[$cal['label']] = array(
                'eventi' => $this->getRepository($cal['entity'])->calendarioMese($this->getUser(), $mese, $anno, $giorno),
                'css' => $cal['css'],
            );
        }

        return array(
            'eventi' => $eventi,
        );
    }

    /**
     * @Route("-aggiungi", name="calendar_aggiungi_evento", options={"expose": true}, defaults={"format": "json"})
     * @Template()
     */
    public function aggiungiAction() {
        $req = $this->getParam('evento');

        $data = \DateTime::createFromFormat('d/m/Y', $req['data']);
        /* @var $data \DateTime */
        $evento = $this->newEvento($req['tipo'], $req['titolo'], $req['note']);
        if ($req['intero'] == 'no') {
            $data->setTime($req['ora'], $req['minuti'], 0);
            $evento->setGiornoIntero(false);
        }
        $evento->setDataOra($data);
        $this->persist($evento);

        return $this->jsonResponse(array('year' => $data->format('Y'), 'month' => $data->format('m')));
    }

}
