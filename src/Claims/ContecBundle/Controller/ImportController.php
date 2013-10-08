<?php

namespace Claims\ContecBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Claims\HBundle\Entity\Pratica;
use Claims\HBundle\Entity\Ospedale;
use Ephp\UtilityBundle\Utility\Debug;
use Ephp\UtilityBundle\Utility\Dom;
use Ephp\UtilityBundle\Utility\String;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;

/**
 * @Route("/import/contec")
 */
class ImportController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\ACLBundle\Controller\Traits\NotifyController,
        \Ephp\UtilityBundle\Controller\Traits\CurlController,
        \Claims\HBundle\Controller\Traits\CalendarController,
        \Claims\HBundle\Controller\Traits\ImportController;

    /**
     * @Route("-form", name="contec_import_form")
     * @Template()
     */
    public function formAction() {
        return array();
    }

    /**
     * @Route("-manuale", name="contec_import_manuale")
     * @Template()
     */
    public function manualeAction() {
        set_time_limit(3600);
        $cliente = $this->getUser()->getCliente();
        $dati = $cliente->getDati();
        $logs = array();
        if (isset($dati['contec'])) {
            $bdxs = $this->enterBdx($dati['contec']);
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
     * @Route("-callback", name="contec_import_callback", options={"expose": true})
     * @Template()
     */
    public function callbackAction() {
        set_time_limit(3600);
        $source = __DIR__ . '/../../../../web'.$this->getParam('file');
        $out = $this->importBdx($this->getUser()->getCliente(), $source);
        return $out;
    }

    /**
     * @Route("-cron", name="contec_import_cron")
     * @Template()
     */
    public function cronAction() {
        foreach ($this->findAll('JFACLBundle:Cliente') as $cliente) {
            /* @var $cliente \JF\ACLBundle\Entity\Cliente */
            $dati = $cliente->getDati();
            if (isset($dati['contec'])) {
                $bdxs = $this->enterBdx($dati['contec']);
                foreach ($bdxs as $bdx) {
                    $this->importBdx($cliente, $bdx);
                }
            }
        }
    }

    private function enterBdx($dati) {
        $sistema = $this->findOneBy('ClaimsHBundle:Sistema', array('nome' => 'Contec'));
        /* @var $sistema \Claims\HBundle\Entity\Sistema */
        $p = array(
            'Username=' . $dati['username'],
            'Password=' . $dati['password'],
            'RimaniConnesso=' . 'SI',
            'PulsanteAccesso=' . 'ACCESSO',
            'HiddenAccesso=' . 'accesso',
        );

        $out = array();

        // https://sistema.contecpartners.com/
        $access = $this->curlPost($sistema->getUrlBase(), implode('&', $p), array('show' => true));

        $matchs = $cookies = array();
        preg_match_all('/Set-Cookie:[^;]+;/', $access, $matchs);

        foreach ($matchs[0] as $match) {
            $cookies[] = trim(str_replace(array('Set-Cookie:', ';'), array('', ''), $match));
        }

        // https://sistema.contecpartners.com/Moduli/San1/Riepilogo/
        sleep(rand(3, 6));
        $this->curlGet($sistema->getUrlBase() . '/Moduli/San1/Riepilogo/', array('cookies' => $cookies));

        //https://sistema.contecpartners.com/Moduli/San1/EsportaInExcelNewLineNuovo/
        sleep(rand(3, 6));
        $out[] = $this->curlGet($sistema->getUrlBase() . '/Moduli/San1/EsportaInExcelNewLineNuovo/', array('cookies' => $cookies));

        //https://sistema.contecpartners.com/Moduli/Inizio/
        sleep(rand(3, 6));
        $this->curlGet($sistema->getUrlBase() . '/Moduli/Inizio/', array('cookies' => $cookies));

        //https://sistema.contecpartners.com/Moduli/SanPie2013/Riepilogo/
        sleep(rand(3, 6));
        $this->curlGet($sistema->getUrlBase() . '/Moduli/SanPie2013/Riepilogo/', array('cookies' => $cookies));

        //https://sistema.contecpartners.com/Moduli/SanPie2013/EsportaInExcelNewLineNuovo/
        sleep(rand(3, 6));
        $out[] = $this->curlGet($sistema->getUrlBase() . '/Moduli/SanPie2013/EsportaInExcelNewLineNuovo/', array('cookies' => $cookies));

        //https://sistema.contecpartners.com/Uscita/
        sleep(rand(3, 6));
        $this->curlGet($sistema->getUrlBase() . '/Uscita/', array('cookies' => $cookies));

        return $out;
    }

    private function importBdx($cliente, $source) {
        $data = new SpreadsheetExcelReader($source, true, 'UTF-8');
        $pratiche_aggiornate = $pratiche_nuove = array();
        $sistema = $this->findOneBy('ClaimsHBundle:Sistema', array('nome' => 'Contec'));
        //return new \Symfony\Component\HttpFoundation\Response(json_encode($data->sheets));
        foreach ($data->sheets as $sheet) {
            $sheet = $sheet['cells'];
            $start = false;
            $colonne = array();
            foreach ($sheet as $riga => $valori_riga) {
                if (!$start) {
                    if (count($colonne) > 0) {
                        $start = true;
                    }
                    if (isset($valori_riga[2]) && in_array($valori_riga[2], array('TPA  Ref.', 'TPA Ref.'))) {
                        $colonne = $valori_riga;
                    }
                } else {
                    if (!isset($valori_riga[2]) || !$valori_riga[2]) {
                        break;
                    } else {
                        try {
                            $this->getEm()->beginTransaction();
                            $pratica = new Pratica();
                            $pratica->setSistema($sistema);
                            $pratica->setCliente($cliente);
                            $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Nuovo')));
                            $pratica->setStatoPratica($this->findOneBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $cliente->getId(), 'primo' => true)));
                            foreach ($valori_riga as $idx => $value) {
                                if (!isset($colonne[$idx])) {
                                    break;
                                }
                                switch ($colonne[$idx]) {
                                    case 'TPA  Ref.':
                                    case 'TPA Ref.':
                                        $pratica->setCodice($value);
                                        $tpa = explode('/', $value);
                                        if (count($tpa) != 3) {
                                            break(3);
                                        }
                                        $ospedale = $this->findOneBy('ClaimsHBundle:Ospedale', array('sigla' => $tpa[0]));
                                        if (!$ospedale) {
                                            $ospedale = new Ospedale();
                                            $ospedale->setSigla($tpa[0]);
                                            $ospedale->setOspedale($tpa[0]);
                                            $ospedale->setSiglaGruppo($tpa[0]);
                                            $ospedale->setGruppo($tpa[0]);
                                            $ospedale->setSistema($sistema);
                                            $this->persist($ospedale);
                                        }
                                        $pratica->setOspedale($ospedale);
                                        $pratica->setAnno($tpa[1]);
                                        $pratica->setTpa($tpa[2]);
                                        break;

                                    case 'CLAYMANT':
                                        $pratica->setClaimant(utf8_encode($value));
                                        break;
                                    case 'DOL':
                                        if ($value) {
                                            $dol = \DateTime::createFromFormat('d/m/Y', $value);
                                            $pratica->setDol($dol);
                                        }
                                        break;
                                    case 'DON':
                                        if ($value) {
                                            $don = \DateTime::createFromFormat('d/m/Y', $value);
                                            $pratica->setDon($don);
                                        }
                                        break;
                                    case 'TYPE OF LOSS':
                                        if ($value) {
                                            $pratica->setTypeOfLoss($value);
                                        }
                                        break;
                                    case 'FIRST RESERVE INDICATION':
                                        if ($value) {
                                            $pratica->setFirstReserveIndication(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'AMOUNT RESERVED':
                                        if ($value) {
                                            if ($value != 'N.P.') {
                                                $pratica->setAmountReserved(String::currency($value, ',', '.'));
                                            } else {
                                                $pratica->setAmountReserved(-1);
                                            }
                                        }
                                        break;
                                    case 'DEDUC. RESERVED':
                                        if ($value) {
                                            if ($value != 'N.P.') {
                                                $pratica->setDeductibleReserved(String::currency($value, ',', '.'));
                                            } else {
                                                $pratica->setDeductibleReserved(-1);
                                            }
                                        }
                                        break;
                                    case 'PROFESS. FEES RESERVE':
                                        if ($value) {
                                            $pratica->setProfessFeesReserve(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'LT FEES RESERVE':
                                        if ($value) {
                                            $pratica->setLtFeesReserve(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'TPA FEES RESERVE':
                                        if ($value) {
                                            $pratica->setTpaFeesReserve(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'POSSIBLE RECOVERY':
                                        if ($value) {
                                            $pratica->setPossibleRecovery(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'AMOUNT SETTLED':
                                        if ($value) {
                                            $pratica->setAmountSettled(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'DEDUC. PAID':
                                        if ($value) {
                                            $pratica->setDeducPaid(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'PROFESS. FEES PAID':
                                        if ($value) {
                                            $pratica->setProfessFeesPaid(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'LT FEES PAID':
                                        if ($value) {
                                            $pratica->setLtFeesPaid(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'TPA FEES PAID':
                                        if ($value) {
                                            $pratica->setTpaFeesPaid(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'TOTAL PAID':
                                        if ($value) {
                                            $pratica->setTotalPaid(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'RECOVERED':
                                        if ($value) {
                                            $pratica->setRecovered(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'TOTAL INCURRED':
                                        if ($value) {
                                            $pratica->setTotalIncurred(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'S.P.':
                                        if ($value) {
                                            $pratica->setSp($value);
                                        }
                                        break;
                                    case 'MPL':
                                        if ($value) {
                                            $pratica->setMpl($value);
                                        }
                                        break;
                                    case 'S.of I.':
                                        if ($value) {
                                            $pratica->setSoi($value);
                                        }
                                        break;
                                    case 'ALL':
                                        if ($value) {
                                            $pratica->setAll($value);
                                        }
                                        break;
                                    case 'COURT':
                                        if ($value) {
                                            $pratica->setCourt($value);
                                        }
                                        break;
                                    case 'REQ':
                                        if ($value) {
                                            $pratica->setReq($value);
                                        }
                                        break;
                                    case 'OTH. POL.':
                                        if ($value) {
                                            $pratica->setOthPol($value);
                                        }
                                        break;
                                    case 'STATUS':
                                        if ($value) {
                                            $pratica->setStatus(utf8_encode($value));
                                        }
                                        break;
                                    case 'RO':
                                        if ($value) {
                                            $pratica->setRo($value);
                                        }
                                        break;
                                    case 'MEDICAL EXAMINER':
                                        if ($value) {
                                            $me = \DateTime::createFromFormat('d/m/Y', $value);
                                            $pratica->setMedicalExaminer($me);
                                        }
                                        break;
                                    case 'LEGAL TEAM':
                                        if ($value) {
                                            $dasc = \DateTime::createFromFormat('d/m/Y', $value);
                                            $pratica->setLegalTeam($dasc);
                                            $pratica->setDasc($dasc);
                                        } else {
                                            $pratica->setDasc(null);
                                        }
                                        break;
                                    case 'COMMENTS':
                                        if ($value) {
                                            $pratica->setComments(utf8_encode($value));
                                        }
                                        break;
                                    default: break;
                                }
                            }
                            $this->salvaPratica($cliente, $pratica, $pratiche_aggiornate, $pratiche_nuove);
                            $this->getEm()->commit();
                        } catch (\Exception $e) {
                            $this->getEm()->rollback();
                            throw $e;
                        }
                    }
                }
            }
        }
        return array('pratiche_aggiornate' => $pratiche_aggiornate, 'pratiche_nuove' => $pratiche_nuove);
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-import-calendario/{slug}", name="contec_import_calendario_manuale", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H"}}})
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