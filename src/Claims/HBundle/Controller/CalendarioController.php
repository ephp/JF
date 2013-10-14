<?php

namespace Claims\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Claims\CoreBundle\Entity\Priorita;
use Claims\CoreBundle\Entity\StatoPratica;
use Claims\HBundle\Entity\Pratica;
use Claims\HBundle\Entity\Evento;
use Claims\HBundle\Entity\Link;
use Claims\HBundle\Entity\Report;
use Claims\HBundle\Form\ReportType;
use JF\ACLBundle\Entity\Gestore;
use Ephp\UtilityBundle\Utility\Time;

/**
 * @Route("/claims-hospital/calendario")
 */
class CalendarioController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController,
        \Ephp\ACLBundle\Controller\Traits\NotifyController,
        Traits\CalendarController;

    /**
     * @Route("/{data}",           name="claims_calendario_hospital",           defaults={"data": "oggi", "mode": "default"},   options={"expose": true, "ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-completo/{data}",  name="claims_calendario_hospital_completo",  defaults={"data": "oggi", "mode": "completo"},  options={"expose": true, "ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-personale/{data}", name="claims_calendario_hospital_personale", defaults={"data": "oggi", "mode": "personale"}, options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Template()
     */
    public function indexAction($data, $mode) {
        $filtri = $this->buildFiltri($data, $mode);
        $pagination = $this->createPagination($this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri), 50);
        return array(
            'pagination' => $pagination,
            'show_gestore' => true,
            'links' => $this->buildLinks(),
            'mode' => $mode,
            'data' => $data,
        );
    }

    /**
     * @Route("-stampa/{data}",           name="claims_calendario_hospital_stampa",           defaults={"data": "oggi", "mode": "default"},   options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-completo/{data}",  name="claims_calendario_hospital_completo_stampa",  defaults={"data": "oggi", "mode": "completo"},  options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-personale/{data}", name="claims_calendario_hospital_personale_stampa", defaults={"data": "oggi", "mode": "personale"}, options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Template()
     */
    public function stampaAction($data, $mode) {
        $filtri = $this->buildFiltri($data, $mode);
        $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();
        return array(
            'entities' => $entities,
            'show_gestore' => true,
            'mode' => $mode,
            'data' => $data,
        );
    }

    private function buildLinks() {
        $out = array();
        if ($this->getUser()->hasRole(array('C_GESTORE_H'))) {
            $out['personale'] = array(
                'route' => 'claims_calendario_hospital_personale',
                'label' => 'Personale'
            );
        }
        if ($this->getUser()->hasRole('C_ADMIN')) {
            $out['completo'] = array(
                'route' => 'claims_calendario_hospital_completo',
                'label' => 'Completo'
            );
        }
        $out['giorno'] = array(
            'fancybox' => 'fb_datapicker',
            'label' => 'Cambia giorno',
            'icon' => 'ico-calendar',
            'class' => 'label-info',
            'target' => '_blank'
        );
        $out['stampa'] = array(
            'route' => $this->getParam('_route') . '_stampa',
            'label' => 'Versione per la stampa',
            'icon' => 'ico-printer',
            'class' => 'label-warning',
            'target' => '_blank'
        );
        return $out;
    }

    private function buildFiltri(&$data, &$mode) {
        $logger = $this->get('logger');
        $cliente = $this->getUser()->getCliente();
        $filtri = array(
            'in' => array(
                'cliente' => $cliente->getId(),
            ),
            'out' => array(
            ),
        );
        $dati = $this->getUser()->getDati();
        switch ($mode) {
            // Legge in cache l'ultimo tipo di visualizzazione
            case 'default':
                $set_default = false;
                if ($this->getUser()->hasRole(array('C_GESTORE_H'))) {
                    if (!isset($dati['claims_h_calendario']) || (!$this->getUser()->hasRole(array('C_ADMIN')) && in_array($dati['claims_h'], array('senza_dasc', 'senza_gestore', 'chiusi')))) {
                        $set_default = true;
                    }
                    $default = 'personale';
                } elseif ($this->getUser()->hasRole(array('C_ADMIN'))) {
                    if (!isset($dati['claims_h_calendario']) || (!$this->getUser()->hasRole(array('C_GESTORE_H')) && in_array($dati['claims_h'], array('personale', 'chiuso')))) {
                        $set_default = true;
                    }
                    $default = 'completo';
                }
                $mode = $set_default ? $default : $dati['claims_h'];
                $logger->notice($mode);
                return $this->buildFiltri($data, $mode);
            // Vede solo 
            case 'personale':
                $filtri['in']['gestore'] = $this->getUser()->getId();
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                $filtri['out']['dasc'] = null;
                break;
            case 'completo':
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                $filtri['out']['dasc'] = null;
                $filtri['out']['gestore'] = null;
                break;
            default:
                break;
        }
        if ($data == 'oggi') {
            $data = new \DateTime();
        } else {
            $data = \DateTime::createFromFormat('d-m-Y', $data);
        }
        $filtri['in']['evento'] = $data;
        $filtri['ricerca'] = array();
        $dati['claims_h_calendario'] = $mode;
        $this->getUser()->setDati($dati);
        $this->persist($this->getUser());
        return $filtri;
    }

}