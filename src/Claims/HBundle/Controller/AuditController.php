<?php

namespace Claims\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Claims\HBundle\Entity\Pratica;
use JF\ACLBundle\Entity\Gestore;
use Ephp\UtilityBundle\Utility\Log;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;

/**
 * @Route("/claims-hospital/audit")
 */
class AuditController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController,
        \Ephp\ACLBundle\Controller\Traits\NotifyController,
        Traits\CalendarController,
        Traits\TabelloneController;

    /**
     * @Route("/",               name="claims_audit_hospital",               defaults={"mode": "default"},       options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-personale/",     name="claims_audit_hospital_personale",     defaults={"mode": "personale"},     options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-completo/",      name="claims_audit_hospital_completo",      defaults={"mode": "completo"},      options={"ACL": {"in_role": {"C_ADMIN", "C_RECUPERI_H"}}})
     * @Route("-senza-gestore/", name="claims_audit_hospital_senza_gestore", defaults={"mode": "senza_gestore"}, options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Template("ClaimsHBundle:Tabellone:index.html.twig")
     */
    public function indexAction($mode) {
        $sorting = $this->sorting();
        $filtri = $this->buildFiltri($mode);
        $pagination = $this->createPagination($this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri), 50);
        $tds = $this->getColonne($mode, $this->V_AUDIT);
        return array(
            'pagination' => $pagination,
            'show_gestore' => true,
            'links' => $this->buildLinks(),
            'mode' => $mode,
            'tds' => $tds,
            'sorting' => $sorting,
            'twig_button' => 'ClaimsHBundle:Audit:button.html.twig',
            'query' => $this->getQuery(),
        );
    }

    /**
     * @Route("-stampa/{monthly_report}",               name="claims_audit_hospital_stampa",               defaults={"monthly_report": false, "mode": "default"},       options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-personale/{monthly_report}",     name="claims_audit_hospital_personale_stampa",     defaults={"monthly_report": false, "mode": "personale"},     options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-completo/{monthly_report}",      name="claims_audit_hospital_completo_stampa",      defaults={"monthly_report": false, "mode": "completo"},      options={"ACL": {"in_role": {"C_ADMIN", "C_RECUPERI_H"}}})
     * @Route("-stampa-senza-gestore/{monthly_report}", name="claims_audit_hospital_senza_gestore_stampa", defaults={"monthly_report": false, "mode": "senza_gestore"}, options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Template()
     */
    public function stampaAction($mode, $monthly_report) {
        $filtri = $this->buildFiltri($mode);
        $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();
        return array(
            'entities' => $entities,
            'show_gestore' => true,
            'mode' => $mode,
            'monthly_report' => $monthly_report !== false,
        );
    }

    /**
     * @Route("-consegna/{sistema}", name="claims_audit_hospital_consegna", options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Template()
     */
    public function consegnaAction($sistema) {
        $tipoEvento = $this->getTipoEvento($this->CONSEGNA_AUDIT);
        /* @var $tipoEvento \Ephp\CalendarBundle\Entity\Tipo */
        try {
            $this->getEm()->beginTransaction();

            $n = $this->updateSql("INSERT INTO claims_h_eventi (
	`pratica_id`, 
	`cliente_id`, 
	`calendario_id`, 
	`tipo_id`, 
	`data_ora`, 
	`giorno_intero`, 
	`titolo`, 
	`note`, 
	`importante`, 
	`delta_g`
)  
	SELECT p.id,
	       :cliente,
	       :calendario,
	       :tipo,
	       now(),
	       :true,
	       :titolo,
	       CONCAT(
                    'AUDIT:\n', 
                    IF(p.audit IS NULL, 'N.P.', p.audit), '\n',
                    'WORSTCASE SCENARIO:\n', 
                    IF(p.worstcase_scenario IS NULL, '0', p.worstcase_scenario), ' €\n',
                    'PROPOSED RESERVE:\n', 
                    IF(p.proposed_reserve IS NULL, '0', p.proposed_reserve), ' €\n',
                    'PERCENT:\n', 
                    IF(p.percentuale IS NULL, '0', p.percentuale), '%\n',
                    'AZIONI FUTURE:\n', 
                    IF(p.azioni IS NULL, 'Non indicate', p.azioni)),
	       :true,
	       :zero
     FROM claims_h_pratiche p
     LEFT JOIN claims_h_ospedali o ON p.ospedale_id = o.id
     LEFT JOIN claims_h_sistemi s ON o.sistema_id = s.id
    WHERE s.nome = :sistema
      AND p.cliente_id = :cliente
;", array(
                'calendario' => $tipoEvento->getCalendario()->getId(),
                'tipo' => $tipoEvento->getId(),
                'titolo' => $tipoEvento->getNome(),
                'true' => true,
                'zero' => 0,
                'sistema' => $sistema,
                'cliente' => $this->getUser()->getCliente()->getId(),
                    )
            );
            $m = $this->updateSql("UPDATE claims_h_pratiche p
            SET p.in_audit = :false
            WHERE p.cliente_id = :cliente
;", array(
                'false' => false,
                'cliente' => $this->getUser()->getCliente()->getId(),
                    )
            );
            $this->getEm()->commit();
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            throw $ex;
        }
        return array(
            'n' => $n,
            'm' => $m,
            'sistema' => $sistema,
        );
    }

    private function buildLinks($full = true) {
        $out = array();
        if ($this->getUser()->hasRole(array('C_GESTORE_H', 'C_RECUPERI_H'))) {
            $out['personale'] = array(
                'route' => 'claims_audit_hospital_personale',
                'label' => 'Personale',
                'icon' => 'ico-user',
            );
        }
        if ($this->getUser()->hasRole(array('C_ADMIN', 'C_RECUPERI_H'))) {
            $out['completo'] = array(
                'route' => 'claims_audit_hospital_completo',
                'label' => 'Completo',
                'icon' => 'ico-group',
            );
        }
        if ($this->getUser()->hasRole('C_ADMIN')) {
            $out['senza_gestore'] = array(
                'route' => 'claims_audit_hospital_senza_gestore',
                'label' => 'Senza gestore',
                'icon' => 'ico-tools',
            );
        }
        $out['search'] = array(
            'fancybox' => 'fb_ricerca',
            'label' => 'Ricerca',
            'class' => $this->getParam('ricerca') ? 'label-success' : 'label-info',
            'icon' => 'ico-search'
        );
        return $out;
    }

    private function buildFiltri(&$mode, &$stato = null) {
        $logger = $this->get('logger');
        $cliente = $this->getUser()->getCliente();
        /* @var $cliente \JF\ACLBundle\Entity\Cliente */
        $filtri = array(
            'in' => array(
                'cliente' => $cliente->getId(),
                'inAudit' => true,
            ),
            'out' => array(
            ),
        );
        $dati = $this->getUser()->getDati();
        switch ($mode) {
            // Legge in cache l'ultimo tipo di visualizzazione
            case 'default':
                $set_default = false;
                if ($this->getUser()->hasRole(array('C_GESTORE_H', 'C_RECUPERI_H'))) {
                    if (!isset($dati['claims_h_mr']) || (!$this->getUser()->hasRole(array('C_ADMIN')) && in_array($dati['claims_h'], array('senza_dasc', 'senza_gestore', 'chiusi')))) {
                        $set_default = true;
                    }
                    $default = 'personale';
                } elseif ($this->getUser()->hasRole(array('C_ADMIN'))) {
                    if (!isset($dati['claims_h_mr']) || (!$this->getUser()->hasRole(array('C_GESTORE_H')) && in_array($dati['claims_h'], array('personale', 'chiuso')))) {
                        $set_default = true;
                    }
                    $default = 'completo';
                }
                $mode = $set_default ? $default : $dati['claims_h'];
                $logger->notice($mode);
                return $this->buildFiltri($mode, $stato);
            case 'personale':
                if ($this->getUser()->hasRole('C_RECUPERI_H')) {
                    $filtri['in']['recuperi'] = $this->getUser()->getId();
                } else {
                    $filtri['in']['gestore'] = $this->getUser()->getId();
                }
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                $filtri['out']['dasc'] = null;
                break;

            case 'completo':
                break;

            case 'senza_gestore':
                $filtri['in']['gestore'] = null;
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                break;
            default:
                break;
        }
        $datiCliente = $cliente->getDati();
        if (isset($datiCliente['slc_h-analisi'])) {
            $analisi = $datiCliente['slc_h-analisi'];
            if (isset($analisi['sigle']) && $analisi['sigle']) {
                $sigle = explode(',', $analisi['sigle']);
                $ospedali = $this->findBy('ClaimsHBundle:Ospedale', array('sigla' => $sigle));
                $idOspedali = array();
                foreach ($ospedali as $ospedale) {
                    $idOspedali[] = $ospedale->getId();
                }
                if (!$this->getUser()->hasRole('C_SUPVIS_H') && $this->getRequest()->get('hidden')) {
                    $filtri['in']['ospedale'] = $idOspedali;
                } else {
                    $filtri['out']['ospedale'] = $idOspedali;
                }
            }
        }
        $filtri['ricerca'] = $this->getParam('ricerca', array());
        $filtri['sorting'] = $dati['claims_h_sorting'];
        $dati['claims_h_mr'] = $mode;
        $this->getUser()->setDati($dati);
        $this->persist($this->getUser());
        return $filtri;
    }
}