<?php

namespace SLC\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/h/slc")
 */
class AnalisiController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController;

    /**
     * @Route("/",                name="slc_hospital",         defaults={"tab": "default"}, options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("/analisi-np",      name="slc_hospital_np",      defaults={"tab": "np"},      options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("/analisi-riserve", name="slc_hospital_riserve", defaults={"tab": "riserve"}, options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Template()
     */
    public function indexAction($tab) {
        $filtri = $this->buildFiltri($tab);
        $pagination = $this->createPagination($this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri), 50);
        return array(
            'links' => $this->buildLinks(),
            'pagination' => $pagination,
            'tabopen' => $tab,
        );
    }
    

    /**
     * @Route("-stampa/{monthly_report}",                 name="slc_hospital_stampa",         defaults={"monthly_report": false, "tab": "default"}, options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-analisi-np/{monthly_report}",      name="slc_hospital_np_stampa",      defaults={"monthly_report": false, "tab": "np"},      options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-analisi-riserve/{monthly_report}", name="slc_hospital_riserve_stampa", defaults={"monthly_report": false, "tab": "riserve"}, options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Template()
     */
    public function stampaAction($tab, $monthly_report) {
        $filtri = $this->buildFiltri($tab);
        $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();
        return array(
            'entities' => $entities,
            'show_gestore' => true,
            'monthly_report' => $monthly_report !== false,
        );
    }
    
    private function buildLinks() {
        $out = array();
        
        $out['np'] = array(
            'label' => 'Analisi N.P.',
            'route' => 'slc_hospital_np',
        );
        
        $out['riserve'] = array(
            'label' => 'Analisi Riserve',
            'route' => 'slc_hospital_riserve',
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
    
    private function buildFiltri(&$tab) {
        $logger = $this->get('logger');
        $cliente = $this->getUser()->getCliente();
        $filtri = array(
            'in' => array(
                'cliente' => $cliente->getId(),
            ),
            'out' => array(
            ),
            'ricerca' => array(
            ),
        );
        $dati = $this->getUser()->getDati();
        switch ($tab) {
            // Legge in cache l'ultimo tipo di visualizzazione
            case 'default':
                $tab = isset($dati['slc_h_tab']) ? $dati['slc_h_tab'] : 'np';
                $logger->notice($tab);
                return $this->buildFiltri($tab);
            // Vede solo 
            case 'np':
                $filtri['in']['amountReserved'] = -1;
                $filtri['sorting'] = '-firstReserveIndication';
                break;
            case 'riserve':
                $filtri['gt']['amountReserved'] = 0;
                $filtri['sorting'] = '-amountReserved';
                break;
            default:
                break;
        }
        $dati['slc_h_tab'] = $tab;
        $this->getUser()->setDati($dati);
        $this->persist($this->getUser());
        return $filtri;
    }
    
    
}
