<?php

namespace Claims\RavinaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Claims\HBundle\Entity\Pratica;
use Claims\HBundle\Entity\Ospedale;
use Ephp\UtilityBundle\Utility\Debug;
use Ephp\UtilityBundle\Utility\Dom;
use Ephp\UtilityBundle\Utility\String;

/**
 * @Route("/import/ravinale")
 */
class ImportController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\ACLBundle\Controller\Traits\NotifyController,
        \Ephp\UtilityBundle\Controller\Traits\CurlController,
        \Claims\HBundle\Controller\Traits\CalendarController;

    /**
     * @Route("-manuale", name="ravinale_import_manuale")
     * @Template()
     */
    public function manualeAction() {
        set_time_limit(3600);
        $cliente = $this->getUser()->getCliente();
        $dati = $cliente->getDati();
        $logs = array();
        if (isset($dati['ravinale'])) {
            $bdxs = $this->enterBdx($dati['ravinale']);
            foreach ($bdxs as $bdx) {
                $logs[] = $this->importBdx($cliente, $bdx);
            }
        }
        $out = array(
            'pratiche_nuove' => array(),
            'pratiche_aggiornate' => array(),
        );
        foreach ($logs as $log) {
            $out['pratiche_nuove'] = array_merge($out['pratiche_nuove'], $log['pratiche_nuove']);
            $out['pratiche_aggiornate'] = array_merge($out['pratiche_aggiornate'], $log['pratiche_aggiornate']);
        }
        return $out;
    }

    /**
     * @Route("-cron", name="ravinale_import_cron")
     * @Template()
     */
    public function cronAction() {
        foreach ($this->findAll('JFACLBundle:Cliente') as $cliente) {
            /* @var $cliente \JF\ACLBundle\Entity\Cliente */
            $dati = $cliente->getDati();
            if (isset($dati['ravinale'])) {
                $bdxs = $this->enterBdx($dati['ravinale']);
                foreach ($bdxs as $bdx) {
                    $this->importBdx($cliente, $bdx);
                }
            }
        }
    }

    private function enterBdx($dati) {
        $sistema = $this->findOneBy('ClaimsHBundle:sistema', array('nome' => 'Ravinale'));
        /* @var $sistema \Claims\HBundle\Entity\Sistema */
        $p = array(
            'Username=' . $dati['username'],
            'Password=' . $dati['password'],
            'RimaniConnesso=' . 'SI',
            'PulsanteAccesso=' . 'ACCESSO',
            'HiddenAccesso=' . 'accesso',
        );

        $out = array();

        // https://sistema.ravinalepartners.com/
        $access = $this->curlPost($sistema->getUrlBase(), implode('&', $p), array('show' => true));

        $matchs = $cookies = array();
        preg_match_all('/Set-Cookie:[^;]+;/', $access, $matchs);

        foreach ($matchs[0] as $match) {
            $cookies[] = trim(str_replace(array('Set-Cookie:', ';'), array('', ''), $match));
        }

        // https://sistema.ravinalepartners.com/Moduli/San1/Riepilogo/
        sleep(rand(3, 6));
        $this->curlGet($sistema->getUrlBase() . '/Moduli/San1/Riepilogo/', array('cookies' => $cookies));

        //https://sistema.ravinalepartners.com/Moduli/San1/EsportaInExcelNewLineNuovo/
        sleep(rand(3, 6));
        $out[] = $this->curlGet($sistema->getUrlBase() . '/Moduli/San1/EsportaInExcelNewLineNuovo/', array('cookies' => $cookies));

        //https://sistema.ravinalepartners.com/Moduli/Inizio/
        sleep(rand(3, 6));
        $this->curlGet($sistema->getUrlBase() . '/Moduli/Inizio/', array('cookies' => $cookies));

        //https://sistema.ravinalepartners.com/Moduli/SanPie2013/Riepilogo/
        sleep(rand(3, 6));
        $this->curlGet($sistema->getUrlBase() . '/Moduli/SanPie2013/Riepilogo/', array('cookies' => $cookies));

        //https://sistema.ravinalepartners.com/Moduli/SanPie2013/EsportaInExcelNewLineNuovo/
        sleep(rand(3, 6));
        $out[] = $this->curlGet($sistema->getUrlBase() . '/Moduli/SanPie2013/EsportaInExcelNewLineNuovo/', array('cookies' => $cookies));

        //https://sistema.ravinalepartners.com/Uscita/
        sleep(rand(3, 6));
        $this->curlGet($sistema->getUrlBase() . '/Uscita/', array('cookies' => $cookies));

        return $out;
    }

    private function importBdx($cliente, $source) {
        /*
          $colonne = array(
          'ID',
          'TPA Ref.',
          'HOSPITAL',
          'CLAIMANT',
          'DOL',
          'DON',
          'TYPE OF LOSS',
          'FIRST RESERVE INDICATION',
          'APPLICABLE DEDUCTIBLE',
          'AMOUNT RESERVED',
          'DEDUCTIBLE RESERVED',
          'LT FEES RESERVE',
          'PROFESS. FEES RESERVE',
          'POSSIBLE RECOVERY',
          'AMOUNT SETTLED',
          'DEDUC. PAID',
          'LT FEES PAID',
          'PROFESS. FEES PAID',
          'TOTAL PAID',
          'RECOVERED',
          'TOTAL<br />INCURRED',
          'S.P.',
          'M.P.L.',
          'S. OF I.',
          'STATUS',
          'COURT',
          'COMMENTS',
          );
         */
        $pratiche_aggiornate = $pratiche_nuove = array();
        $sistema = $this->findOneBy('ClaimsHBundle:Sistema', array('nome' => 'Ravinale'));

        $colonne = array();
        $doc = new \DOMDocument();
        $doc->loadHTML($source);

        $tag_html = Dom::getDOMBase($doc);
        $tag_body = Dom::getDOMElement($tag_html, array('tag' => 'body'));
        $table = Dom::getDOMElement($tag_body, array('tag' => 'table'));
        $trs = Dom::getDOMElement($table, array('tag' => 'tr'), false);

        foreach ($trs as $tr) {
            $tds = Dom::getDOMElement($tr, array('tag' => 'td'), false);
            if (count($tds) == 0) {
                $ths = Dom::getDOMElement($tr, array('tag' => 'th'), false);
                foreach ($ths as $idx => $th) {
                    $colonne[$idx] = $th->nodeValue;
                }
            }
            if (count($tds) > 0) {
                try {
                    $this->getEm()->beginTransaction();
                    $pratica = new Pratica();
                    $pratica->setSistema($sistema);
                    $pratica->setCliente($cliente);
                    $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Nuovo')));
                    $pratica->setStatoPratica($this->findOneBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $cliente->getId(), 'primo' => true)));
                    foreach ($tds as $idx => $td) {
                        switch ($colonne[$idx]) {
                            case 'TPA Ref.':
                                $pratica->setCodice($td->nodeValue);
                                $tpa = explode('/', $td->nodeValue);
                                if (count($tpa) != 2) {
                                    break(3);
                                }
                                $tpa2 = explode('-', $tpa[0]);
                                if (count($tpa2) != 2) {
                                    break(3);
                                }
                                $ospedale = $this->findOneBy('ClaimsHBundle:Ospedale', array('sigla' => $tpa2[0]));
                                if (!$ospedale) {
                                    $ospedale = new Ospedale();
                                    $ospedale->setSigla($tpa2[0]);
                                    $ospedale->setOspedale($tds[$idx + 1]->nodeValue);
                                    $ospedale->setSiglaGruppo('Piemonte');
                                    $ospedale->setGruppo('Ospedali Piemonte');
                                    $ospedale->setSistema($sistema);
                                    $this->persist($ospedale);
                                }
                                $pratica->setOspedale($ospedale);
                                $pratica->setAnno($tpa2[1]);
                                $pratica->setTpa($tpa[1]);
                                break;

                            case 'CLAIMANT':
                                $pratica->setClaimant($td->nodeValue);
                                break;

                            case 'DOL':
                                if ($td->nodeValue) {
                                    $dol = \DateTime::createFromFormat('d/m/Y', $td->nodeValue);
                                    $pratica->setDol($dol);
                                }
                                break;

                            case 'DON':
                                if ($td->nodeValue) {
                                    $don = \DateTime::createFromFormat('d/m/Y', $td->nodeValue);
                                    $pratica->setDon($don);
                                }
                                break;

                            case 'TYPE OF LOSS':
                                if ($td->nodeValue) {
                                    $pratica->setTypeOfLoss($td->nodeValue);
                                }
                                break;


                            case 'FIRST RESERVE INDICATION':
                                if ($td->nodeValue) {
                                    $pratica->setFirstReserveIndication(String::currency($td->nodeValue));
                                }
                                break;

                            case 'APPLICABLE DEDUCTIBLE':
                                $pratica->setApplicableDeductible(String::currency($td->nodeValue));
                                break;

                            case 'AMOUNT RESERVED':
                                if ($td->nodeValue) {
                                    if ($td->nodeValue != 'NP') {
                                        $pratica->setAmountReserved(String::currency($td->nodeValue));
                                    } else {
                                        $pratica->setAmountReserved(-1);
                                    }
                                }
                                break;

                            case 'DEDUCTIBLE RESERVED':
                                if ($td->nodeValue) {
                                    if ($td->nodeValue != 'NP') {
                                        $pratica->setDeductibleReserved(String::currency($td->nodeValue));
                                    } else {
                                        $pratica->setDeductibleReserved(-1);
                                    }
                                }
                                break;

                            case 'LT FEES RESERVE':
                                $pratica->setLtFeesReserve(String::currency($td->nodeValue));
                                break;

                            case 'PROFESS. FEES RESERVE':
                                $pratica->setProfessFeesReserve(String::currency($td->nodeValue));
                                break;

                            case 'POSSIBLE RECOVERY':
                                $pratica->setPossibleRecovery(String::currency($td->nodeValue));
                                break;

                            case 'AMOUNT SETTLED':
                                $pratica->setAmountSettled(String::currency($td->nodeValue));
                                break;

                            case 'DEDUC. PAID':
                                $pratica->setDeducPaid(String::currency($td->nodeValue));
                                break;

                            case 'LT FEES PAID':
                                $pratica->setLtFeesPaid(String::currency($td->nodeValue));
                                break;

                            case 'PROFESS. FEES PAID':
                                $pratica->setProfessFeesPaid(String::currency($td->nodeValue));
                                break;

                            case 'TOTAL PAID':
                                $pratica->setTotalPaid(String::currency($td->nodeValue));
                                break;

                            case 'RECOVERED':
                                $pratica->setRecovered(String::currency($td->nodeValue));
                                break;

                            case 'TOTAL<br />INCURRED':
                            case 'TOTALINCURRED':
                                $pratica->setTotalIncurred(String::currency($td->nodeValue));
                                break;

                            case 'S.P.':
                                if ($td->nodeValue) {
                                    $pratica->setSp($td->nodeValue);
                                }
                                break;
                            case 'M.P.L.':
                                if ($td->nodeValue) {
                                    $pratica->setMpl($td->nodeValue);
                                }
                                break;

                            case 'S. OF I.':
                                if ($td->nodeValue) {
                                    $pratica->setSoi($td->nodeValue);
                                }
                                break;

                            case 'STATUS':
                                if ($td->nodeValue) {
                                    $pratica->setStatus($td->nodeValue);
                                }
                                break;

                            case 'COURT':
                                if ($td->nodeValue) {
                                    $pratica->setCourt($td->nodeValue);
                                }
                                break;

                            case 'COMMENTS':
                                if ($td->nodeValue) {
                                    $pratica->setComments($td->nodeValue);
                                }
                                break;

                            default: break;
                        }
                    }
                    $old = $this->findOneBy('ClaimsHBundle:Pratica', array('cliente' => $cliente->getId(), 'codice' => $pratica->getCodice()));
                    /* @var $old Pratica */
                    if ($old) {
                        $log = array();
                        if ($old->getPriorita() && $old->getPriorita()->getPriorita() == 'Chiuso') {
                            if ($old->getStatus() != $pratica->getStatus()) {
                                $log[] = "Pratica messa in priorità 'Riaperta'";
                                $old->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Riaperto')));
                            }
                        }
                        if ($old->getDol()->format('d-m-Y') != $pratica->getDol()->format('d-m-Y')) {
                            $log[] = "DOL: da '" . $old->getDol()->format('d-m-Y') . "' a '" . $pratica->getDol()->format('d-m-Y') . "'";
                            $old->setDol($pratica->getDol());
                        }
                        if ($old->getDon()->format('d-m-Y') != $pratica->getDon()->format('d-m-Y')) {
                            $log[] = "DON: da '" . $old->getDon()->format('d-m-Y') . "' a '" . $pratica->getDon()->format('d-m-Y') . "'";
                            ;
                            $old->setDon($pratica->getDon());
                        }
                        if ($old->getTypeOfLoss() != $pratica->getTypeOfLoss()) {
                            $log[] = "TYPE OF LOSS: da '" . $old->getTypeOfLoss() . "' a '" . $pratica->getTypeOfLoss() . "'";
                            ;
                            $old->setTypeOfLoss($pratica->getTypeOfLoss());
                        }
                        if ($old->getFirstReserveIndication() != $pratica->getFirstReserveIndication()) {
                            $log[] = "FIRST RESERVE INDICATION: da '" . $old->getFirstReserveIndication() . "' a '" . $pratica->getFirstReserveIndication() . "'";
                            ;
                            $old->setFirstReserveIndication($pratica->getFirstReserveIndication());
                        }
                        if ($old->getApplicableDeductible() != $pratica->getApplicableDeductible()) {
                            $log[] = "APPLICABLE DEDUCTIBLE: da '" . $old->getApplicableDeductible() . "' a '" . $pratica->getApplicableDeductible() . "'";
                            ;
                            $old->setApplicableDeductible($pratica->getApplicableDeductible());
                        }
                        if (($old->getAmountReserved() < 0 ? 'NP' : $old->getAmountReserved()) != ($pratica->getAmountReserved() < 0 ? 'NP' : $pratica->getAmountReserved())) {
                            $log[] = "AMOUNT RESERVED: da '" . ($old->getAmountReserved() < 0 ? 'NP' : $old->getAmountReserved()) . "' a '" . ($pratica->getAmountReserved() < 0 ? 'NP' : $pratica->getAmountReserved()) . "'";
                            ;
                            $old->setAmountReserved($pratica->getAmountReserved());
                        }
                        if (($old->getDeductibleReserved() < 0 ? 'NP' : $old->getDeductibleReserved()) != ($pratica->getDeductibleReserved() < 0 ? 'NP' : $pratica->getDeductibleReserved())) {
                            $log[] = "DEDUCTIBLE RESERVED: da '" . ($old->getDeductibleReserved() < 0 ? 'NP' : $old->getDeductibleReserved()) . "' a '" . ($pratica->getDeductibleReserved() < 0 ? 'NP' : $pratica->getDeductibleReserved()) . "'";
                            ;
                            $old->setDeductibleReserved($pratica->getDeductibleReserved());
                        }
                        if ($old->getLtFeesReserve() != $pratica->getLtFeesReserve()) {
                            $log[] = "LT FEES RESERVE: da '" . $old->getLtFeesReserve() . "' a '" . $pratica->getLtFeesReserve() . "'";
                            ;
                            $old->setLtFeesReserve($pratica->getLtFeesReserve());
                        }
                        if ($old->getProfessFeesReserve() != $pratica->getProfessFeesReserve()) {
                            $log[] = "PROFESS. FEES RESERVE: da '" . $old->getProfessFeesReserve() . "' a '" . $pratica->getProfessFeesReserve() . "'";
                            ;
                            $old->setProfessFeesReserve($pratica->getProfessFeesReserve());
                        }
                        if ($old->getPossibleRecovery() != $pratica->getPossibleRecovery()) {
                            $log[] = "POSSIBLE RECOVERY: da '" . $old->getPossibleRecovery() . "' a '" . $pratica->getPossibleRecovery() . "'";
                            ;
                            $old->setPossibleRecovery($pratica->getPossibleRecovery());
                        }
                        if ($old->getAmountSettled() != $pratica->getAmountSettled()) {
                            $log[] = "AMOUNT SETTLED: da '" . $old->getAmountSettled() . "' a '" . $pratica->getAmountSettled() . "'";
                            ;
                            $old->setAmountSettled($pratica->getAmountSettled());
                        }
                        if ($old->getDeducPaid() != $pratica->getDeducPaid()) {
                            $log[] = "DEDUC. PAID: da '" . $old->getDeducPaid() . "' a '" . $pratica->getDeducPaid() . "'";
                            ;
                            $old->setDeducPaid($pratica->getDeducPaid());
                        }
                        if ($old->getLtFeesPaid() != $pratica->getLtFeesPaid()) {
                            $log[] = "LT FEES PAID: da '" . $old->getLtFeesPaid() . "' a '" . $pratica->getLtFeesPaid() . "'";
                            ;
                            $old->setLtFeesPaid($pratica->getLtFeesPaid());
                        }
                        if ($old->getProfessFeesPaid() != $pratica->getProfessFeesPaid()) {
                            $log[] = "PROFESS. FEES PAID: da '" . $old->getProfessFeesPaid() . "' a '" . $pratica->getProfessFeesPaid() . "'";
                            ;
                            $old->setProfessFeesPaid($pratica->getProfessFeesPaid());
                        }
                        if ($old->getTotalPaid() != $pratica->getTotalPaid()) {
                            $log[] = "TOTAL PAID: da '" . $old->getTotalPaid() . "' a '" . $pratica->getTotalPaid() . "'";
                            ;
                            $old->setTotalPaid($pratica->getTotalPaid());
                        }
                        if ($old->getRecovered() != $pratica->getRecovered()) {
                            $log[] = "RECOVERED: da '" . $old->getRecovered() . "' a '" . $pratica->getRecovered() . "'";
                            ;
                            $old->setRecovered($pratica->getRecovered());
                        }
                        if ($old->getTotalIncurred() != $pratica->getTotalIncurred()) {
                            $log[] = "TOTAL INCURRED: da '" . $old->getTotalIncurred() . "' a '" . $pratica->getTotalIncurred() . "'";
                            ;
                            $old->setTotalIncurred($pratica->getTotalIncurred());
                        }
                        if ($old->getSp() != $pratica->getSp()) {
                            $log[] = "S.P.: da '" . $old->getSp() . "' a '" . $pratica->getSp() . "'";
                            ;
                            $old->setSp($pratica->getSp());
                        }
                        if ($old->getMpl() != $pratica->getMpl()) {
                            $log[] = "M.P.L.: da '" . $old->getMpl() . "' a '" . $pratica->getMpl() . "'";
                            ;
                            $old->setMpl($pratica->getMpl());
                        }
                        if ($old->getSoi() != $pratica->getSoi()) {
                            $log[] = "S. OF I.: da '" . $old->getSoi() . "' a '" . $pratica->getSoi() . "'";
                            ;
                            $old->setSoi($pratica->getSoi());
                        }
                        if ($old->getStatus() != $pratica->getStatus()) {
                            $log[] = "STATUS: da '" . $old->getStatus() . "' a '" . $pratica->getStatus() . "'";
                            ;
                            $old->setStatus($pratica->getStatus());
                        }
                        if ($old->getCourt() != $pratica->getCourt()) {
                            $log[] = "COURT: da '" . $old->getCourt() . "' a '" . $pratica->getCourt() . "'";
                            ;
                            $old->setCourt($pratica->getCourt());
                        }
                        if ($old->getComments() != $pratica->getComments()) {
                            $log[] = "COMMENTS: da '" . $old->getComments() . "' a '" . $pratica->getComments() . "'";
                            ;
                            $old->setComments($pratica->getComments());
                        }

                        if (count($log) > 0) {
                            $old->addLog($log);
                            $this->persist($old);
                            $pratiche_aggiornate[] = $old;
                        }
                    } else {
                        $pratica->addLog(array('Importata pratica'));
                        $this->persist($pratica);
                        $pratiche_nuove[] = $pratica;
                    }
                    $this->getEm()->commit();
                } catch (\Exception $e) {
                    $this->getEm()->rollback();
                    throw $e;
                }
            }
        }
        return array('pratiche_nuove' => $pratiche_nuove, 'pratiche_aggiornate' => $pratiche_aggiornate);
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-import-calendario/{slug}", name="ravinale_import_calendario_manuale", options={"expose": true, "ACL": {"in_role": {"C_GESTORE", "C_GESTORE_H"}}})
     * @Template("ClaimsHBundle:Tabellone:pratica/calendario.html.twig")
     */
    public function calendarioAction($slug) {
        $colonne = array('data', 'note');
        set_time_limit(3600);
        $csv = $this->getParam('import');
        $entity = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $entity Pratica */
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Scheda entity.');
        }

        $cal = $this->getCalendar();
        $righe = explode("\n", str_replace(array("\r", "\n\n"), array("\n", "\n"), $csv));
        foreach ($righe as $riga) {
            $dati = explode("\t", $riga);
            if (count($dati) >= 2) {
                try {
                    $this->getEm()->beginTransaction();
                    $data = \DateTime::createFromFormat('d/m/Y', substr($dati[0], 0, 10));
                    /* @var $data \DateTime */
                    $evento = $this->newEvento($this->RAVINALE, $entity, 'Ravinale Piemonte', $dati[1]);
                    $evento->setDataOra($data);
                    $olds = $this->findBy('ClaimsHBundle:Evento', array(
                        'calendario' => $cal->getId(),
                        'tipo' => $evento->getTipo()->getId(),
                        'pratica' => $entity->getId(),
                        'note' => $evento->getNote(),
                    ));
//                    Debug::vd($old);
                    if (!$olds) {
                        $this->persist($evento);
                    } else {
                        $data->setTime(0, 0, 0);
                        $save = true;
                        foreach ($olds as $old) {
                            $old->getDataOra()->setTime(0, 0, 0);
                            if ($data->getTimestamp() == $old->getDataOra()->getTimestamp()) {
                                $save = false;
                            }
                        }
                        if ($save) {
                            $this->persist($evento);
                        }
                    }

                    $this->getEm()->commit();
                } catch (\Exception $e) {
                    $this->getEm()->rollback();
                    throw $e;
                }
            }
        }
        return array('entity' => $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug)));
    }

}