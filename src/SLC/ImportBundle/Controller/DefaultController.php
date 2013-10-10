<?php

namespace SLC\ImportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/slc")
 * @Template()
 */
class DefaultController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Claims\HBundle\Controller\Traits\CalendarController,
        \Ephp\UtilityBundle\Controller\Traits\CurlController;

    /**
     * @Route("/claims", name="slc_import_claims", options={"ACL": {"in_role": {"R_SUPER"}}})
     * @Route("/all_claims", name="slc_import_all_claims", options={"ACL": {"in_role": {"R_SUPER"}}})
     */
    public function claimsAction() {
        $dati = $this->getUser()->getCliente()->getDati();
        if (!isset($dati['slc_h_import-import']['url_base'])) {
            return $this->createNotFoundException('Configurare la sorgente dati di JF-CLAIMS');
        }

        if($this->getParam('_route') == 'slc_import_all_claims') {
            $pratiche = $this->executeSql('SELECT p.slug FROM claims_h_pratiche p WHERE p.cliente_id = ' . $this->getUser()->getCliente()->getId());
        } else {    
            $pratiche = $this->executeSql('SELECT p.slug FROM claims_h_pratiche p WHERE p.cliente_id = ' . $this->getUser()->getCliente()->getId().' AND p.gestore_id IS NULL');
        }

        foreach ($pratiche as $slug) {
            $slug = $slug['slug'];
            
            $this->claim($slug, $dati);
        }
        return $this->redirect($this->generateUrl('claims_hospital'));
    }

    /**
     * @Route("/claim/{slug}", name="slc_import_claim", options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     */
    public function claimAction($slug) {
        $dati = $this->getUser()->getCliente()->getDati();
        if (!isset($dati['slc_h_import-import']['url_base'])) {
            return $this->createNotFoundException('Configurare la sorgente dati di JF-CLAIMS');
        }

        $out = $this->claim($slug, $dati);
        
        return $out ? $this->redirect($this->generateUrl('claims_hospital')) : $this->redirect($this->generateUrl('claims_hospital_pratica', array('slug' => $slug)));
    }

    private function claim($slug, $dati) {
        set_time_limit(3600);
        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica \Claims\HBundle\Entity\Pratica */
        $url = "{$dati['slc_h_import-import']['url_base']}export-scheda/" . str_replace(' ', '%20', $pratica->getOspedale()->getSigla()) . "/{$pratica->getAnno()}/{$pratica->getTpa()}/esporta-cron";
        try {
            $this->getEm()->beginTransaction();
            $out = $this->curlGet($url);
            $_pratica = json_decode($out);
            if (!$pratica->getDasc() && $_pratica->dasc) {
                $pratica->setDasc(\DateTime::createFromFormat('Y-m-d', $_pratica->dasc));
            }
            if ($_pratica->gestore) {
                $pratica->setGestore($this->findOneBy('JFACLBundle:Gestore', array('cliente' => $this->getUser()->getCliente()->getId(), 'sigla' => $_pratica->gestore)));
            }
            if ($_pratica->priorita) {
                $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => $_pratica->priorita)));
            }
            if ($_pratica->stato) {
                $pratica->setStatoPratica($this->findOneBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $this->getUser()->getCliente()->getId(), 'stato' => $_pratica->stato)));
            }
            $pratica->setNote($_pratica->note);
            $pratica->setLegaliAvversari($_pratica->avversari);
            $pratica->setDatiRecupero($_pratica->dati);
            $pratica->setSettlementAuthority($_pratica->sa);
            $pratica->setOffertaNostra($_pratica->offerta_nostra);
            $pratica->setOffertaLoro($_pratica->offerta_loro);
            $pratica->setRecuperoOffertaNostra($_pratica->recupero_offerta_nostra);
            $pratica->setRecuperoOffertaLoro($_pratica->recupero_offerta_loro);

            foreach ($_pratica->report as $key => $val) {
                switch ($key) {
                    case 'dol':
                    case 'don':
                        if ($val) {
                            $set = \Doctrine\Common\Util\Inflector::camelize('set_report_' . $key);
                            $pratica->$set(\DateTime::createFromFormat('Y-m-d', $_pratica->dasc));
                        }
                        break;
                    case 'gestore':
                        if ($val) {
                            $set = \Doctrine\Common\Util\Inflector::camelize('set_report_' . $key);
                            $pratica->$set($this->findOneBy('JFACLBundle:Gestore', array('cliente' => $this->getUser()->getCliente()->getId(), 'sigla' => $val)));
                        }
                        break;
                    case 'reports':
                        foreach ($val as $_report) {
                            $report = $this->findOneBy('ClaimsHBundle:Report', array('pratica' => $pratica->getId(), 'number' => $_report->number));
                            if (!$report) {
                                $report = new \Claims\HBundle\Entity\Report();
                                $report->setPratica($pratica);
                                $report->setNumber($_report->number);
                            }
                            $report->setAnalisiDanno($_report->analisi_danno);
                            $report->setAzioni($_report->azioni);
                            $report->setCopertura($_report->copertura);
                            $report->setData(\DateTime::createFromFormat('Y-m-d', $_report->data));
                            $report->setDescrizioneInFatto($_report->descrizione_in_fatto);
                            $report->setMedicoLegale1($_report->medico_legale1);
                            $report->setMedicoLegale2($_report->medico_legale2);
                            $report->setMedicoLegale3($_report->medico_legale3);
                            $report->setNote($_report->note);
                            $report->setPossibileRivalsa($_report->possibile_rivalsa);
                            $report->setRelazioneAvversaria($_report->relazione_avversaria);
                            $report->setRelazioneExAdverso($_report->relazione_ex_adverso);
                            $report->setRichiestaSa($_report->richiesta_sa);
                            $report->setRiserva($_report->riserva);
                            $report->setStato($_report->stato);
                            $report->setValidato($_report->validato);
                            $report->setValutazioneResponsabilita($_report->valutazione_responsabilita);
                            $this->persist($report);
                        }
                        break;
                    default:
                        $set = \Doctrine\Common\Util\Inflector::camelize('set_report_' . $key);
                        $pratica->$set($val);
                        break;
                }
            }

            foreach ($_pratica->links as $_link) {
                $link = $this->findOneBy('ClaimsHBundle:Link', array('pratica' => $pratica->getId(), 'url' => $_link->url));
                if (!$link) {
                    $link = new \Claims\HBundle\Entity\Link();
                    $link->setPratica($pratica);
                    $link->setUrl($_link->url);
                }
                $link->setSito($_link->sito);
                $this->persist($link);
            }

            foreach ($_pratica->eventi as $_evento) {
                $tipo = $this->getTipoEvento($_evento->tipo);
                if (!$tipo) {
                    throw new \Exception("Tipo '{$_evento->tipo}' non corretto");
                }
                $dataOra = \DateTime::createFromFormat('Y-m-d', $_evento->data_ora);
                /* @var $dataOra \DateTime */
                $dataOra->setTime(8, 0, 0);
                $evento = $this->findOneBy('ClaimsHBundle:Evento', array('pratica' => $pratica->getId(), 'tipo' => $tipo->getId(), 'data_ora' => $dataOra, 'titolo' => $_evento->titolo));
                if (!$evento) {
                    $evento = $this->newEvento($_evento->tipo, $pratica, $_evento->titolo);
                    $evento->setDataOra($dataOra);
                }
                $evento->setCliente($this->getUser()->getCliente());
                $evento->setDeltaG($_evento->delta_g);
                $evento->setGestore($pratica->getGestore());
                $evento->setGiornoIntero($_evento->giorno_intero);
                $evento->setImportante($_evento->importante);
                $evento->setNote($_evento->note);
                $this->persist($evento);
            }
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        return !is_null($pratica->getGestore());
    }

}
