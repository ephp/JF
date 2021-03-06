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

        if ($this->getParam('_route') == 'slc_import_all_claims') {
            $pratiche = $this->executeSql('SELECT p.slug FROM claims_h_pratiche p WHERE p.cliente_id = ' . $this->getUser()->getCliente()->getId());
        } else {
            $pratiche = $this->executeSql('SELECT p.slug FROM claims_h_pratiche p WHERE p.cliente_id = ' . $this->getUser()->getCliente()->getId() . ' AND p.gestore_id IS NULL');
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

        $no_gestore = $this->claim($slug, $dati);

        return $no_gestore ? $this->redirect($this->generateUrl('claims_hospital')) : $this->redirect($this->generateUrl('claims_hospital_pratica', array('slug' => $slug)));
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
            $save_pratica = false;
            if (!$pratica->getDasc() && $_pratica->dasc) {
                $save_pratica = true;
                $pratica->setDasc(\DateTime::createFromFormat('Y-m-d', $_pratica->dasc));
            }
            if ($_pratica->gestore) {
                $gestore = $this->findOneBy('JFACLBundle:Gestore', array('cliente' => $this->getUser()->getCliente()->getId(), 'sigla' => $_pratica->gestore));
                if (!$pratica->getGestore() || $pratica->getGestore()->getId() != $gestore->getId()) {
                    $save_pratica = true;
                    $pratica->setGestore($gestore);
                }
            }
            if ($_pratica->priorita) {
                $priorita = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => $_pratica->priorita));
                if (!$priorita) {
                    $priorita = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                }
                if (!$pratica->getPriorita() || $pratica->getPriorita()->getId() != $priorita->getId()) {
                    $save_pratica = true;
                    $pratica->setPriorita($priorita);
                }
            }
            if ($_pratica->stato) {
                $statoPratica = $this->findOneBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $this->getUser()->getCliente()->getId(), 'stato' => $_pratica->stato));
                if ($statoPratica && (!$pratica->getStatoPratica() || $pratica->getStatoPratica()->getId() != $statoPratica->getId())) {
                    $save_pratica = true;
                    $pratica->setStatoPratica($statoPratica);
                }
            }
            if ($_pratica->note && $pratica->getNote() != $_pratica->note) {
                $save_pratica = true;
                $pratica->setNote($_pratica->note);
            }
            if ($_pratica->avversari && $pratica->getLegaliAvversari() != $_pratica->avversari) {
                $save_pratica = true;
                $pratica->setLegaliAvversari($_pratica->avversari);
            }
            if ($_pratica->dati && $pratica->getDatiRecupero() != $_pratica->dati) {
                $save_pratica = true;
                $pratica->setDatiRecupero($_pratica->dati);
            }
            if ($_pratica->sa && $pratica->getSettlementAuthority() != $_pratica->sa) {
                $save_pratica = true;
                $pratica->setSettlementAuthority($_pratica->sa);
            }
            if ($_pratica->offerta_nostra && $pratica->getOffertaNostra() != $_pratica->offerta_nostra) {
                $save_pratica = true;
                $pratica->setOffertaNostra($_pratica->offerta_nostra);
            }
            if ($_pratica->offerta_loro && $pratica->getOffertaLoro() != $_pratica->offerta_loro) {
                $save_pratica = true;
                $pratica->setOffertaLoro($_pratica->offerta_loro);
            }
            /*
              if ($_pratica->recupero_offerta_nostra && $pratica->getRecuperoOffertaNostra() != $_pratica->recupero_offerta_nostra) {
              $save_pratica = true;
              $pratica->setRecuperoOffertaNostra($_pratica->recupero_offerta_nostra);
              }
              if ($_pratica->recupero_offerta_loro && $pratica->getRecuperoOffertaLoro() != $_pratica->recupero_offerta_loro) {
              $save_pratica = true;
              $pratica->setRecuperoOffertaLoro($_pratica->recupero_offerta_loro);
              }
             */

            
            foreach ($_pratica->report as $key => $val) {
                switch ($key) {
                    case 'dol':
                    case 'don':
                        if ($val) {
                            $get = \Doctrine\Common\Util\Inflector::camelize('get_report_' . $key);
                            $data = \DateTime::createFromFormat('Y-m-d', $val);
                            if (!$pratica->$get() || $pratica->$get()->format('d-m-Y') != $data->format('d-m-Y')) {
                                $save_pratica = true;
                                $set = \Doctrine\Common\Util\Inflector::camelize('set_report_' . $key);
                                $pratica->$set($data);
                            }
                        }
                        break;
                    case 'gestore':
                        if ($val) {
                            $get = \Doctrine\Common\Util\Inflector::camelize('get_report_' . $key);
                            $gestore = $this->findOneBy('JFACLBundle:Gestore', array('cliente' => $this->getUser()->getCliente()->getId(), 'sigla' => $val));
                            if (!$pratica->$get() || $pratica->$get()->getId() != $gestore->getId()) {
                                $save_pratica = true;
                                $set = \Doctrine\Common\Util\Inflector::camelize('set_report_' . $key);
                                $pratica->$set($gestore);
                            }
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
                        $get = \Doctrine\Common\Util\Inflector::camelize('get_report_' . $key);
                        if ($pratica->$get() != $val) {
                            $save_pratica = true;
                            $set = \Doctrine\Common\Util\Inflector::camelize('set_report_' . $key);
                            $pratica->$set($val);
                        }
                        break;
                }
            }

            foreach ($_pratica->links as $_link) {
                $link = $this->findOneBy('ClaimsHBundle:Link', array('pratica' => $pratica->getId(), 'url' => $_link->url));
                $salvaLink = false;
                if (!$link) {
                    $salvaLink = true;
                    $link = new \Claims\HBundle\Entity\Link();
                    $link->setPratica($pratica);
                    $link->setUrl($_link->url);
                }
                if ($_link->sito && $link->getSito() != $_link->sito) {
                    $salvaLink = true;
                    $link->setSito($_link->sito);
                }
                if ($salvaLink) {
                    $this->persist($link);
                }
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
                $salvaEvento = false;
                if (!$evento) {
                    $salvaEvento = true;
                    $evento = $this->newEvento($_evento->tipo, $pratica, $_evento->titolo);
                    $evento->setDataOra($dataOra);
                    $evento->setCliente($this->getUser()->getCliente());
                    $evento->setGestore($pratica->getGestore());
                    $evento->setGiornoIntero(true);
                }
                if ($_evento->delta_g && $evento->getDeltaG() != $_evento->delta_g) {
                    $salvaEvento = true;
                    $evento->setDeltaG($_evento->delta_g);
                }
                if ($evento->getImportante() != $_evento->importante) {
                    $salvaEvento = true;
                    $evento->setImportante($_evento->importante);
                }
                if ($_evento->note && $evento->getNote() != $_evento->note) {
                    $salvaEvento = true;
                    $evento->setNote($_evento->note);
                }
                if ($salvaEvento) {
                    $this->persist($evento);
                }
            }
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        return is_null($pratica->getGestore());
    }

}

