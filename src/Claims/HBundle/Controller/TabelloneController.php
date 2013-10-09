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
 * @Route("/claims-hospital")
 */
class TabelloneController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController,
        \Ephp\ACLBundle\Controller\Traits\NotifyController,
        Traits\CalendarController;

    /**
     * @Route("/",                 name="claims_hospital",               defaults={"mode": "default"},       options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}})
     * @Route("-completo/",        name="claims_hospital_completo",      defaults={"mode": "completo"},      options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}})
     * @Route("-personale/",       name="claims_hospital_personale",     defaults={"mode": "personale"},     options={"ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Route("-chiusi/",          name="claims_hospital_chiuso",        defaults={"mode": "chiuso"},        options={"ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Route("-senza-dasc/",      name="claims_hospital_senza_dasc",    defaults={"mode": "senza_dasc"},    options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-senza-gestore/",   name="claims_hospital_senza_gestore", defaults={"mode": "senza_gestore"}, options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-chiusi-completo/", name="claims_hospital_chiusi",        defaults={"mode": "chiusi"},        options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Template()
     */
    public function indexAction($mode) {
        $filtri = $this->buildFiltri($mode);
        $pagination = $this->createPagination($this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri), 50);
        return array(
            'pagination' => $pagination,
            'show_gestore' => true,
            'links' => $this->buildLinks(),
            'mode' => $mode,
        );
    }

    /**
     * @Route("-stati/{stato}",           name="claims_stati_hospital",           defaults={"mode": "default", "stato": "default"},   options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}})
     * @Route("-stati-completo/{stato}",  name="claims_stati_hospital_completo",  defaults={"mode": "completo", "stato": "default"},  options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-stati-personale/{stato}", name="claims_stati_hospital_personale", defaults={"mode": "personale", "stato": "default"}, options={"ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Template("ClaimsHBundle:Tabellone:index.html.twig")
     */
    public function statiAction($mode, $stato) {
        $filtri = $this->buildFiltri($mode, $stato);
        $pagination = $this->createPagination($this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri), 50);
        return array(
            'pagination' => $pagination,
            'show_gestore' => true,
            'links' => $this->buildLinks(false),
            'mode' => $mode,
            'stati' => $this->findBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $this->getUser()->getCliente()->getId(), 'tab' => true)),
            'stato' => $stato,
        );
    }

    /**
     * @Route("-stampa/",                 name="claims_hospital_stampa",               defaults={"mode": "default"},       options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}})
     * @Route("-stampa-completo/",        name="claims_hospital_completo_stampa",      defaults={"mode": "completo"},      options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}})
     * @Route("-stampa-personale/",       name="claims_hospital_personale_stampa",     defaults={"mode": "personale"},     options={"ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Route("-stampa-chiusi/",          name="claims_hospital_chiuso_stampa",        defaults={"mode": "chiuso"},        options={"ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Route("-stampa-senza-dasc/",      name="claims_hospital_senza_dasc_stampa",    defaults={"mode": "senza_dasc"},    options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-stampa-senza-gestore/",   name="claims_hospital_senza_gestore_stampa", defaults={"mode": "senza_gestore"}, options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-stampa-chiusi-completo/", name="claims_hospital_chiusi_stampa",        defaults={"mode": "chiusi"},        options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Template()
     */
    public function stampaAction($mode) {
        $filtri = $this->buildFiltri($mode);
        $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();
        return array(
            'entities' => $entities,
            'show_gestore' => true,
            'mode' => $mode,
        );
    }

    /**
     * @Route("-stati-stampa/{stato}",           name="claims_stati_hospital_stampa",           defaults={"mode": "default", "stato": "default"},   options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}})
     * @Route("-stati-stampa-completo/{stato}",  name="claims_stati_hospital_completo_stampa",  defaults={"mode": "completo", "stato": "default"},  options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-stati-stampa-personale/{stato}", name="claims_stati_hospital_personale_stampa", defaults={"mode": "personale", "stato": "default"}, options={"ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Template("ClaimsHBundle:Tabellone:stampa.html.twig")
     */
    public function stampaStatiAction($mode, $stato) {
        $filtri = $this->buildFiltri($mode, $stato);
        $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();
        return array(
            'entities' => $entities,
            'show_gestore' => true,
            'mode' => $mode,
        );
    }

    private function buildLinks($full = true) {
        $out = array();
        if ($full) {
            if ($this->getUser()->hasRole(array('C_GESTORE_H'))) {
                $out['personale'] = array(
                    'route' => 'claims_hospital_personale',
                    'label' => 'Personale'
                );
                $out['chiuso'] = array(
                    'route' => 'claims_hospital_chiuso',
                    'label' => 'Chiusi'
                );
            }
            $out['completo'] = array(
                'route' => 'claims_hospital_completo',
                'label' => 'Completo'
            );
            if ($this->getUser()->hasRole('C_ADMIN')) {
                $out['chiusi'] = array(
                    'route' => 'claims_hospital_chiusi',
                    'label' => 'Tutti i chiusi'
                );
                $out['senza_dasc'] = array(
                    'route' => 'claims_hospital_senza_dasc',
                    'label' => 'Senza DASC'
                );
                $out['senza_gestore'] = array(
                    'route' => 'claims_hospital_senza_gestore',
                    'label' => 'Senza gestore'
                );
            }
        } else {
            if ($this->getUser()->hasRole(array('C_GESTORE_H'))) {
                $out['personale'] = array(
                    'route' => 'claims_stati_hospital_personale',
                    'label' => 'Personale'
                );
            }
            if ($this->getUser()->hasRole('C_ADMIN')) {
                $out['completo'] = array(
                    'route' => 'claims_stati_hospital_completo',
                    'label' => 'Completo'
                );
            }
        }
        $out['search'] = array(
            'fancybox' => 'fb_ricerca',
            'label' => 'Ricerca',
            'class' => $this->getParam('ricerca') ? 'label-success' : 'label-info',
            'icon' => 'ico-search'
        );
        $out['stampa'] = array(
            'route' => $this->getParam('_route') . '_stampa',
            'label' => 'Versione per la stampa',
            'icon' => 'ico-printer',
            'class' => 'label-warning',
            'target' => '_blank'
        );
        if ($this->getParam('ricerca')) {
            $out['stampa']['params'] = array('ricerca' => $this->getParam('ricerca'));
        }
        return $out;
    }

    private function buildFiltri(&$mode, &$stato = null) {
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
        if ($stato) {
            if (!in_array($mode, array("default", "completo", "personale"))) {
                $mode = 'default';
            }
            if ($stato == 'default') {
                if(isset($dati['claims_h_stato'])) {
                    $stato = $dati['claims_h_stato'];
                    unset($dati['claims_h']);
                } else {
                    $stato = $this->findOneBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $this->getUser()->getCliente()->getId(), 'tab' => true))->getId();
                }
            }
        }
        switch ($mode) {
            // Legge in cache l'ultimo tipo di visualizzazione
            case 'default':
                $set_default = false;
                if ($this->getUser()->hasRole(array('C_GESTORE_H'))) {
                    if (!isset($dati['claims_h']) || (!$this->getUser()->hasRole(array('C_ADMIN')) && in_array($dati['claims_h'], array('senza_dasc', 'senza_gestore', 'chiusi')))) {
                        $set_default = true;
                    }
                    $default = 'personale';
                } elseif ($this->getUser()->hasRole(array('C_ADMIN'))) {
                    if (!isset($dati['claims_h']) || (!$this->getUser()->hasRole(array('C_GESTORE_H')) && in_array($dati['claims_h'], array('personale', 'chiuso')))) {
                        $set_default = true;
                    }
                    $default = 'completo';
                }
                $mode = $set_default ? $default : $dati['claims_h'];
                $logger->notice($mode);
                return $this->buildFiltri($mode, $stato);
            // Vede solo 
            case 'personale':
                $filtri['in']['gestore'] = $this->getUser()->getId();
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                $filtri['out']['dasc'] = null;
                break;
            case 'chiuso':
                $filtri['in']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                $filtri['in']['gestore'] = $this->getUser()->getId();
                break;
            case 'completo':
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                $filtri['out']['dasc'] = null;
                $filtri['out']['gestore'] = null;
                break;
            case 'chiusi':
                $filtri['in']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                break;
            case 'senza_dasc':
                $filtri['in']['dasc'] = null;
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                break;
            case 'senza_gestore':
                $filtri['in']['gestore'] = null;
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                $filtri['out']['dasc'] = null;
                break;
            default:
                break;
        }
        if ($stato) {
            $dati['claims_h_stato'] = $stato;
            $filtri['in']['statoPratica'] = $stato;
        }
        $filtri['ricerca'] = $this->getParam('ricerca', array());
        $dati['claims_h'] = $mode;
        $this->getUser()->setDati($dati);
        $this->persist($this->getUser());
        return $filtri;
    }

    /**
     * @Route("-cambia-priorita/", name="claims_hospital_cambia_priorita", options={"expose": true, "ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}}, defaults={"_format": "json"})
     */
    public function cambiaPrioritaAction() {
        $req = $this->getParam('priorita');

        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $req['id']));
        /* @var $pratica Pratica */
        $priorita = $this->find('ClaimsCoreBundle:Priorita', $req['priorita']);
        /* @var $priorita Priorita */

        try {
            $this->getEm()->beginTransaction();
            if ($priorita->getOnAssign() == 'cal') {
                $evento = $this->newEvento($this->PRIORITA, $pratica, $priorita->getPriorita(), "Cambio priorità da {$pratica->getPriorita()->getPriorita()} a {$priorita->getPriorita()}");
                $this->persist($evento);
            }
            $pratica->setPriorita($priorita);
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        return $this->jsonResponse(array('id' => $priorita->getId(), 'label' => $priorita->getPriorita(), 'css' => $priorita->getCss()));
    }

    /**
     * @Route("-cambia-gestore/", name="claims_hospital_cambia_gestore", options={"expose": true, "ACL": {"in_role": {"C_ADMIN"}}}, defaults={"_format": "json"})
     */
    public function cambiaGestoreAction() {
        $req = $this->getParam('gestore');

        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $req['id']));
        /* @var $pratica Pratica */
        $gestore = $this->findOneBy('JFACLBundle:Gestore', array('slug' => $req['gestore']));
        /* @var $gestore Gestore */

        $genera = false;
        try {
            $this->getEm()->beginTransaction();
            if ($pratica->getGestore()) {
                $evento = $this->newEvento($this->CAMBIO_GESTORE, $pratica, "Cambio gestore", "Da {$pratica->getGestore()->getNome()} a {$gestore->getNome()}");
                $this->persist($evento);
            } else {
                $genera = true;
            }
            if (!$pratica->getDasc()) {
                $pratica->setDasc(new \DateTime());
            }
            if (in_array($pratica->getPriorita()->getPriorita(), array('Nuovo', 'Normale'))) {
                $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Assegnato')));
            }
            $pratica->setGestore($gestore);
            if ($genera) {
                $this->checkAttivita($pratica);
            }
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        try {
            $this->notify($gestore, '[JF-CLAIMS Hospital] Ti è stato assegnata la pratica ' . $pratica, 'ClaimsHBundle:email:nuovo_sinistro', array('pratica' => $pratica));
        } catch (\Exception $e) {
            throw $e;
        }
        $priorita = $pratica->getPriorita();
        return $this->jsonResponse(array('nome' => $gestore->getNome(), 'slug' => $gestore->getSlug(), 'sigla' => $gestore->getSigla(), 'id' => $priorita->getId(), 'label' => $priorita->getPriorita(), 'css' => $priorita->getCss(), 'dasc' => $pratica->getDasc()->format('d-m-Y')));
    }

    /**
     * @Route("-cambia-dasc/", name="claims_hospital_cambia_dasc", options={"expose": true, "ACL": {"in_role": {"C_ADMIN"}}}, defaults={"_format": "json"})
     */
    public function cambiaDascAction() {
        $req = $this->getParam('dasc');

        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $req['id']));
        /* @var $pratica Pratica */

        $dasc = \DateTime::createFromFormat('Y-m-d', $req['dasc']);

        try {
            $this->getEm()->beginTransaction();
            $pratica->setDasc($dasc);
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        return $this->jsonResponse(array('dasc' => $pratica->getDasc()->format('d-m-Y')));
    }

    /**
     * @Route("-get-note/{slug}", name="claims_hospital_get_note", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}}, defaults={"_format": "json"})
     */
    public function getNoteAction($slug) {

        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica Pratica */

        return $this->jsonResponse(array('note' => $pratica->getNote()));
    }

    /**
     * @Route("-cambia-note/", name="claims_hospital_cambia_note", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}}, defaults={"_format": "json"})
     */
    public function cambiaNoteAction() {
        $req = $this->getParam('note');

        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $req['id']));
        /* @var $pratica Pratica */

        $note = $req['note'];

        try {
            $this->getEm()->beginTransaction();

            if (in_array($pratica->getPriorita()->getPriorita(), array('Nuovo', 'Assegnato'))) {
                $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Normale')));
            }

            $pratica->setNote($note);
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        $priorita = $pratica->getPriorita();
        return $this->jsonResponse(array('note' => \Ephp\UtilityBundle\Utility\String::tronca($pratica->getNote(), 100), 'id' => $priorita->getId(), 'label' => $priorita->getPriorita(), 'css' => $priorita->getCss()));
    }

    /**
     * @Route("-cambia-stato/", name="claims_hospital_cambia_stato", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}}, defaults={"_format": "json"})
     */
    public function cambiaStatoAction() {
        $req = $this->getParam('stato');

        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $req['id']));
        /* @var $pratica Pratica */

        $stato = $this->find('ClaimsCoreBundle:StatoPratica', $req['stato']);
        /* @var $stato StatoPratica */

        try {
            $this->getEm()->beginTransaction();

            $oldPriorita = false;
            if ($stato->getChiudi()) {
                $oldPriorita = $pratica->getPriorita();
                $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso')));
            } elseif (in_array($pratica->getPriorita()->getPriorita(), array('Nuovo', 'Assegnato'))) {
                $oldPriorita = $pratica->getPriorita();
                $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Normale')));
            }

            if ($stato->getAnnota()) {
                $evento = $this->newEvento($this->CAMBIO_STATO_OPERATIVO, $pratica, $stato->getStato(), "Cambio stato pratica da {$pratica->getStatoPratica()->getStato()} a {$stato->getStato()}");
                $this->persist($evento);
            }
            if ($oldPriorita && $pratica->getPriorita()->getOnAssign() == 'cal') {
                $evento = $this->newEvento($this->PRIORITA, $pratica, $pratica->getPriorita()->getPriorita(), "Cambio priorità da {$oldPriorita->getPriorita()} a {$pratica->getPriorita()->getPriorita()}");
                $this->persist($evento);
            }

            $pratica->setStatoPratica($stato);
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        $priorita = $pratica->getPriorita();
        return $this->jsonResponse(array('stato' => $stato->getStato(), 'stato_id' => $stato->getId(), 'id' => $priorita->getId(), 'label' => $priorita->getPriorita(), 'css' => $priorita->getCss()));
    }

    /**
     * @Route("-pratica/{slug}", name="claims_hospital_pratica", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Route("-dettagli/{slug}", name="claims_hospital_dettagli", options={"expose": true, "ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}})
     * @Route("-report/{slug}", name="claims_hospital_report", options={"expose": true, "ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}})
     * @Route("-stampa-report/{slug}", name="claims_hospital_stampa_report", options={"expose": true, "ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}})
     * @Route("-stampa-pratica/{slug}", name="claims_hospital_stampa_pratica", options={"expose": true, "ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H"}}})
     */
    public function praticaAction($slug) {

        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica Pratica */

        if ($this->getUser()->getCliente()->getId() != $pratica->getCliente()->getId()) {
            return $this->createNotFoundException('Utente non autorizzato');
        }

        switch ($this->getParam('_route')) {
            case "claims_hospital_dettagli":
                $twig = 'ClaimsHBundle:Tabellone:dettagli.html.twig';
                break;
            case "claims_hospital_report":
                $twig = 'ClaimsHBundle:Tabellone:pratica/report.html.twig';
                break;
            case "claims_hospital_stampa_pratica":
                $twig = 'ClaimsHBundle:Tabellone:stampaPratica.html.twig';
                break;
            case "claims_hospital_stampa_report":
                $twig = 'ClaimsHBundle:Tabellone:stampaReport.html.twig';
                break;
            case "claims_hospital_pratica":
            default:
                $this->checkAttivita($pratica);
                $this->checkReport($pratica);
                $twig = 'ClaimsHBundle:Tabellone:pratica.html.twig';
                break;
        }

        return $this->render($twig, array('entity' => $pratica));
    }

    /**
     * @Route("-autoupdate-pratica/{slug}", name="claims_hospital_pratica_autoupdate", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}}, defaults={"_format": "json"})
     */
    public function autoupdatePraticaAction($slug) {
        $req = $this->getParam('pratica');

        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica Pratica */
        try {
            $fx = \Doctrine\Common\Util\Inflector::camelize("set_{$req['field']}");
            switch ($req['field']) {
                case 'dasc':
                case 'report_dol':
                case 'report_don':
                    $data = \DateTime::createFromFormat('d/m/Y', $req['value']);
                    $pratica->$fx($data);
                    break;
                case 'report_amount_reserved':
                    if ($req['value'] == 'N.P.') {
                        $pratica->$fx(-1);
                        break;
                    }
                default:
                    $pratica->$fx($req['value']);
                    break;
            }

            if (in_array($pratica->getPriorita()->getPriorita(), array('Nuovo', 'Assegnato'))) {
                $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Normale')));
            }

            $this->persist($pratica);
        } catch (\Exception $e) {
            throw $e;
        }
        return new \Symfony\Component\HttpFoundation\Response(json_encode($req));
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-aggiungi-link/{slug}", name="claims_hospital_aggiungi_link", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Template("ClaimsHBundle:Tabellone:pratica/links.html.twig")
     */
    public function aggiungiLinkAction($slug) {
        $req = $this->getParam('link');
        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $entity Pratica */
        if (!$pratica) {
            throw $this->createNotFoundException('Unable to find Scheda entity.');
        }
        $link = new Link();
        $link->setPratica($pratica);
        $link->setUrl($req['url']);
        $link->setSito($req['sito']);
        $this->persist($link);

        if (in_array($pratica->getPriorita()->getPriorita(), array('Nuovo', 'Assegnato'))) {
            $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Normale')));
            $this->persist($pratica);
        }

        return array('entity' => $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug)));
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-cancella-link", name="claims_hospital_cancella_link", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Template("ClaimsHBundle:Tabellone:pratica/links.html.twig")
     */
    public function cancellaLinkAction() {
        $req = $this->getParam('link');
        $entity = $this->find('ClaimsHBundle:Link', $req['id']);
        /* @var $entity Link */
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Scheda entity.');
        }
        $id = $entity->getPratica()->getId();
        $this->remove($entity);
        return array('entity' => $this->find('ClaimsHBundle:Pratica', $id));
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-aggiungi-evento/{slug}", name="claims_hospital_aggiungi_evento", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Template("ClaimsHBundle:Tabellone:pratica/calendario.html.twig")
     */
    public function aggiungiEventoAction($slug) {
        $req = $this->getParam('evento');
        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $entity Pratica */
        if (!$pratica) {
            throw $this->createNotFoundException('Unable to find Scheda entity.');
        }

        $data = \DateTime::createFromFormat('d/m/Y', $req['data']);
        $evento = $this->newEvento($this->ATTIVITA_MANUALE, $pratica, $req['titolo'], $req['note']);
        $evento->setDataOra($data);
        $this->persist($evento);

        if (in_array($pratica->getPriorita()->getPriorita(), array('Nuovo', 'Assegnato'))) {
            $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Normale')));
            $this->persist($pratica);
        }

        return array('entity' => $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug)));
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-cancella-evento", name="claims_hospital_cancella_evento", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Template("ClaimsHBundle:Tabellone:pratica/calendario.html.twig")
     */
    public function cancellaEventoAction() {
        $req = $this->getParam('evento');
        $entity = $this->find('ClaimsHBundle:Evento', $req['id']);
        /* @var $entity Evento */
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Scheda entity.');
        }
        $id = $entity->getPratica()->getId();
        $this->remove($entity);
        return array('entity' => $this->find('ClaimsHBundle:Pratica', $id));
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-evidenzia-evento", name="claims_hospital_evidenzia_evento", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}}, defaults={"_format"="json"})
     */
    public function evidenziaEventoAction() {
        $req = $this->getParam('evento');
        $evento = $this->find('ClaimsHBundle:Evento', $req['id']);
        /* @var $evento Evento */
        if (!$evento) {
            throw $this->createNotFoundException('Unable to find Scheda entity.');
        }
        $evento->setImportante(!$evento->getImportante());
        $this->persist($evento);
        $out = array('id' => 'star_' . $req['id'], 'color' => $evento->getImportante() ? '#FFAA31 !important;' : '#D1D1D1 !important;', 'remove' => $evento->getImportante() ? 'ico-star-empty' : 'ico-star', 'add' => $evento->getImportante() ? 'ico-star' : 'ico-star-empty');
        return $this->jsonResponse($out);
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-autoupdate-calendario", name="claims_hospital_evento_autoupdate", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}}, defaults={"_format"="json"})
     */
    public function autoupdateCalendarioAction() {
        $req = $this->getParam('evento');
        $req['reload'] = 0;

        $evento = $this->find('ClaimsHBundle:Evento', $req['id']);
        /* @var $evento Evento */
        try {
            $this->getEm()->beginTransaction();
            $fx = \Doctrine\Common\Util\Inflector::camelize("set_{$req['field']}");
            switch ($req['field']) {
                case 'data_ora':
                    $req['reload'] = 1;
                    if ($req['value'] == '') {
                        $this->remove($evento);
                    } else {
                        $old_data = $evento->getDataOra();
                        $data = \DateTime::createFromFormat('d/m/Y', $req['value']);
                        $evento->$fx($data);
                        $generatore = $this->generatore();
                        $rischedulato = false;
                        foreach ($generatore as $i => $gen) {
                            if ($rischedulato) {
                                $data = Time::calcolaData($data, $gen['giorni']);
                                $tipo = $this->getTipoEvento($gen['tipo']);
                                if (isset($gen['from'])) {
                                    $tmp = $this->findBy('ClaimsHBundle:Evento', array('pratica' => $evento->getPratica()->getId(), 'tipo' => $tipo->getId()), array(), 1, $gen['from']);
                                    $eventoP = $tmp[0];
                                } else {
                                    $eventoP = $this->findOneBy('ClaimsHBundle:Evento', array('pratica' => $evento->getPratica()->getId(), 'tipo' => $tipo->getId()));
                                }
                                $eventoP->$fx($data);
                                $this->persist($eventoP);
                            }
                            if ($evento->getTipo()->getSigla() == $gen['tipo']) {
                                if (!$rischedulato) {
                                    $rischedulato = true;
                                    $eventoR = $this->newEvento($this->RISCHEDULAZIONE, $evento->getPratica(), 'Rischedulazione', "{$evento->getTipo()->getNome()} (da " . date('d-m-Y', $old_data->getTimestamp()) . " a " . date('d-m-Y', $data->getTimestamp()) . ")");
                                    $this->persist($eventoR);
                                    $req['reload'] = 1;
                                }
                            }
                        }
                    }
                    break;
                default:
                    $evento->$fx($req['value']);
                    break;
            }
            $this->persist($evento);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        return new \Symfony\Component\HttpFoundation\Response(json_encode($req));
    }

    /**
     * Displays a form to create a new Report entity.
     *
     * @Route("-report-pratica/{slug}", name="claims_hospital_report_pratica_new", options={"ACL": {"in_role": {"C_GESTORE_H"}}})
     */
    public function newReportAction($slug) {
        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica Pratica */

        if ($this->getUser()->getCliente()->getId() != $pratica->getCliente()->getId()) {
            return $this->createNotFoundException('Utente non autorizzato');
        }

        $old = $this->findOneBy('ClaimsHBundle:Report', array('pratica' => $pratica->getId()), array('number' => 'DESC'));
        /* @var $old Report */

        $entity = new Report();
        if ($old) {
            $entity->setNumber($old->getNumber() + 1);
            $entity->setAnalisiDanno($old->getAnalisiDanno());
            $entity->setAzioni($old->getAzioni());
            $entity->setCopertura($old->getCopertura());
            $entity->setDescrizioneInFatto($old->getDescrizioneInFatto());
            $entity->setMedicoLegale1($old->getMedicoLegale1());
            $entity->setMedicoLegale2($old->getMedicoLegale2());
            $entity->setMedicoLegale3($old->getMedicoLegale3());
            $entity->setNote($old->getNote());
            $entity->setPossibileRivalsa($old->getPossibileRivalsa());
            $entity->setRelazioneAvversaria($old->getRelazioneAvversaria());
            $entity->setRelazioneExAdverso($old->getRelazioneExAdverso());
            $entity->setRichiestaSa($old->getRichiestaSa());
            $entity->setRiserva($old->getRiserva());
            $entity->setStato($old->getStato());
            $entity->setValutazioneResponsabilita($old->getValutazioneResponsabilita());
        } else {
            $entity->setNumber('1');
        }
        $entity->setPratica($pratica);
        $entity->setData(new \DateTime());
        $entity->setValidato(false);

        $this->persist($entity);
        return $this->redirect($this->generateUrl('claims_hospital_report_pratica_edit', array('slug' => $slug, 'numero' => $entity->getNumber())));
    }

    /**
     * Displays a form to edit an existing Report entity.
     *
     * @Route("-report-pratica-modifica/{slug}/{numero}", name="claims_hospital_report_pratica_edit", options={"ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Template()
     */
    public function editReportAction($slug, $numero) {
        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica Pratica */

        if ($this->getUser()->getCliente()->getId() != $pratica->getCliente()->getId()) {
            return $this->createNotFoundException('Utente non autorizzato');
        }

        $entity = $this->findOneBy('ClaimsHBundle:Report', array('pratica' => $pratica->getId(), 'number' => $numero));
        /* @var $entity Report */

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Report entity.');
        }

        $editForm = $this->createEditReportForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Report entity.
     *
     * @Route("-report-pratica/{slug}/{numero}", name="claims_hospital_report_pratica_show", options={"ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Template()
     */
    public function showReportAction($slug, $numero) {
        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica Pratica */

        if ($this->getUser()->getCliente()->getId() != $pratica->getCliente()->getId()) {
            return $this->createNotFoundException('Utente non autorizzato');
        }

        $entity = $this->findOneBy('ClaimsHBundle:Report', array('pratica' => $pratica->getId(), 'number' => $numero));
        /* @var $entity Report */

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Report entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Creates a form to edit a Report entity.
     *
     * @param Report $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditReportForm(Report $entity) {
        $form = $this->createForm(new ReportType(), $entity, array(
            'action' => $this->generateUrl('claims_hospital_report_pratica_update', array('slug' => $entity->getPratica()->getSlug(), 'numero' => $entity->getNumber())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Conferma', 'attr' => array('class' => 'btn no-display')));

        return $form;
    }

    /**
     * Edits an existing Report entity.
     *
     * @Route("-report-pratica-salva/{slug}/{numero}", name="claims_hospital_report_pratica_update", options={"ACL": {"in_role": {"C_GESTORE_H"}}})
     * @Template("ClaimsHBundle:Report:edit.html.twig")
     */
    public function updateReportAction($slug, $numero) {
        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica Pratica */

        if ($this->getUser()->getCliente()->getId() != $pratica->getCliente()->getId()) {
            return $this->createNotFoundException('Utente non autorizzato');
        }

        $entity = $this->findOneBy('ClaimsHBundle:Report', array('pratica' => $pratica->getId(), 'number' => $numero));
        /* @var $entity Report */

        $editForm = $this->createEditReportForm($entity);
        $editForm->handleRequest($this->getRequest());

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('claims_hospital_report', array('slug', $pratica->getSlug())));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Report entity.
     *
     * @Route("-report-pratica-autoupdate/{slug}/{numero}", name="claims_hospital_report_pratica_autoupdate", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}}, defaults={"_format": "json"})
     * @Template("ClaimsHBundle:Report:edit.html.twig")
     */
    public function autoupdateReportAction($slug, $numero) {
        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica Pratica */

        if ($this->getUser()->getCliente()->getId() != $pratica->getCliente()->getId()) {
            return $this->createNotFoundException('Utente non autorizzato');
        }

        $entity = $this->findOneBy('ClaimsHBundle:Report', array('pratica' => $pratica->getId(), 'number' => $numero));
        /* @var $entity Report */

        $req = $this->getParam('report');

        try {
            $fx = \Doctrine\Common\Util\Inflector::camelize("set_{$req['field']}");
            $entity->$fx($req['value']);

            if (in_array($pratica->getPriorita()->getPriorita(), array('Nuovo', 'Assegnato'))) {
                $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Normale')));
                $this->persist($pratica);
            }

            $this->persist($entity);
        } catch (\Exception $e) {
            throw $e;
        }
        return new \Symfony\Component\HttpFoundation\Response(json_encode($req));
    }

    private function checkAttivita(Pratica &$pratica) {
        if (!$pratica->getGestore()) {
            throw new \Exception('Assegnare gestore');
        }
        if ($this->countSql('claims_h_eventi e, 
                             cal_tipi t 
                       WHERE e.tipo_id = t.id
                         AND e.pratica_id = :pratica
                         AND t.sigla = :sigla', array('pratica' => $pratica->getId(), 'sigla' => $this->ANALISI_SINISTRI_COPERTURA)) == 0) {
            try {
                $this->getEm()->beginTransaction();
                $generatore = $this->generatore();
                if (!$pratica->getDasc()) {
                    $pratica->setDasc(new \DateTime());
                    $this->persist($pratica);
                }
                $data = $pratica->getDasc();
                foreach ($generatore as $gen) {
                    $data = Time::calcolaData($data, $gen['giorni']);
                    $evento = $this->newEvento($gen['tipo'], $pratica);
                    $evento->setDataOra($data)
                            ->setDeltaG($gen['giorni'])
                            ->setImportante(true);
                    $this->persist($evento);
                }
                $this->getEm()->commit();
            } catch (\Exception $e) {
                $this->getEm()->rollback();
                throw $e;
            }
            $pratica = $this->find('ClaimsHBundle:Pratica', $pratica->getId());
        }
    }

    private function checkReport(Pratica $pratica) {
        if (!$pratica->getReportGestore()) {
            $pratica->setReportAmountReserved($pratica->getAmountReserved());
            $pratica->setReportApplicableDeductable($pratica->getApplicableDeductible());
            $pratica->setReportDol($pratica->getDol());
            $pratica->setReportDon($pratica->getDon());
            $pratica->setReportGestore($pratica->getGestore());
            $pratica->setReportPossibleRecovery($pratica->getPossibleRecovery());
            $pratica->setReportServiceProvider($pratica->getSp());
            $pratica->setReportSoi($pratica->getSoi());
            $pratica->setReportTypeOfLoss($pratica->getTypeOfLoss());
            $this->persist($pratica);
        }
    }

}