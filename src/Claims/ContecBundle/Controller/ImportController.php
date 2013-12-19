<?php

namespace Claims\ContecBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Claims\HBundle\Entity\Pratica;
use Claims\HBundle\Entity\Sistema;
use Claims\HBundle\Entity\Ospedale;
use Claims\HBundle\Entity\Documento;
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
        $out = array();
        if (isset($dati['cl_h_contec-import'])) {
            $bdx = $this->enterBdx($dati['cl_h_contec-import']);
            $source = __DIR__ . '/../../../../web/uploads/contec' . $cliente->getId() . '.xls';
            if (file_exists($source)) {
                unlink($source);
            }
            $xls = fopen($source, 'w');
            fwrite($xls, $bdx);
            fclose($xls);
//            chmod($source, 0777);
            $out = $this->importBdx($cliente, $source);
        }
        return $out;
    }

    /**
     * @Route("-callback", name="contec_import_callback", options={"expose": true})
     * @Template()
     */
    public function callbackAction() {
        set_time_limit(3600);
        $source = __DIR__ . '/../../../../web' . $this->getParam('file');
        $audit = $this->getParam('mr') == 'checked';
        if ($audit) {
            $this->getRepository('ClaimsHBundle:Pratica')->cancellaAudit($this->getUser()->getCliente());
        }
        $out = $this->importBdx($this->getUser()->getCliente(), $source, $audit);
        return $out;
    }

    /**
     * @Route("-cron", name="contec_import_cron", defaults={"_format": "json"})
     * @Template()
     */
    public function cronAction() {
        foreach ($this->findAll('JFACLBundle:Cliente') as $cliente) {
            /* @var $cliente \JF\ACLBundle\Entity\Cliente */
            set_time_limit(3600);
            $dati = $cliente->getDati();
            $out = array();
            if (isset($dati['cl_h_contec-import'])) {
                $bdx = $this->enterBdx($dati['cl_h_contec-import']);
                $source = __DIR__ . '/../../../../web/uploads/contec' . $cliente->getId() . '.xls';
                if (file_exists($source)) {
                    unlink($source);
                }
                $xls = fopen($source, 'w');
                fwrite($xls, $bdx);
                fclose($xls);
//            chmod($source, 0777);
                $out[$cliente->getId()] = $this->importBdx($cliente, $source);
            }
        }
        foreach ($out as $cliente_id => $elenchi) {
            foreach ($elenchi as $nome_elenco => $elenco) {
                foreach ($elenco as $i => $pratica) {
                    $out[$cliente_id][$nome_elenco][$i] = $pratica->getSlug();
                }
            }
        }
        return $this->jsonResponse($out);
    }

    /**
     * @Route("-schede-cron", name="contec_import_schede_cron", defaults={"_format": "json"})
     */
    public function cronSchedeAction() {
        $ora = new \DateTime();
        $h = $ora->format('H');
        $m = intval($ora->format('i'));
        $d = $ora->format('N');
        $out = array('d' => $d, 'h' => $h, 'm' => $m);
        if ($d >= 6) {
            if ($h < 6) {
                return $this->jsonResponse($out);
            }
        } else {
            if ($h < 6) {
                return $this->jsonResponse($out);
            }
            if (($h >= 8 && $h <= 20) && !in_array($m, array(59,0, 4,5, 9,10, 14,15, 19,20, 24,25, 29,30, 34,35, 40,41, 44,45, 50,51, 54,55))) {
                return $this->jsonResponse($out);
            }
        }
        try {
            foreach ($this->findAll('JFACLBundle:Cliente') as $cliente) {
                /* @var $cliente \JF\ACLBundle\Entity\Cliente */
                $dati = $cliente->getDati();
                if (isset($dati['cl_h_contec-import'])) {
                    set_time_limit(3600);
                    $sistema = $this->findOneBy('ClaimsHBundle:Sistema', array('nome' => 'Contec'));
                    /* @var $sistema \Claims\HBundle\Entity\Sistema */
                    $ospedali = $this->findBy('ClaimsHBundle:Ospedale', array('sistema' => $sistema->getId()));
                    $o = array();
                    foreach ($ospedali as $ospedale) {
                        $o[] = $ospedale->getId();
                    }
                    $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('cliente' => $cliente->getId(), 'ospedale' => $o), array('alignedAt' => 'ASC', 'inAudit' => 'DESC', 'inMonthlyReport' => 'DESC', 'anno' => 'ASC', 'codice' => 'ASC'));
                    /* @var $pratica \Claims\HBundle\Entity\Pratica */
                    $pratica->setAlignedAt(new \DateTime());
                    $this->persist($pratica);
                    list($cookies, $reqTime) = $this->login($sistema, $dati['cl_h_contec-import']);
                    $out[$cliente->getId()][] = $this->enterScheda($sistema, $pratica, $cookies, $reqTime);
                    $this->logout($sistema, $cookies);
                }
            }
        } catch (\Exception $e) {
            $out['error'] = $e->getMessage();
            return $this->jsonResponse($out);
        }
        return $this->jsonResponse($out);
    }

    private function importBdx(\JF\ACLBundle\Entity\Cliente $cliente, $source, $audit = false) {
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
                    if (isset($valori_riga[2]) && in_array($valori_riga[2], array('TPA  Ref.', 'TPA Ref.'))) {
                        $colonne = $valori_riga;
                        $start = true;
                    }
                } else {
                    if (!isset($valori_riga[2]) || !$valori_riga[2]) {
                        continue;
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
                                    continue;
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
                            $this->salvaPratica($cliente, $pratica, $pratiche_aggiornate, $pratiche_nuove, $audit);
                            $this->getEm()->commit();
                        } catch (\Exception $e) {
                            $this->getEm()->rollback();
                            throw $e;
                        }
                    }
                }
            }
        }
        $aggiornamenti = array();
        foreach ($pratiche_aggiornate as $pratica) {
            /* @var $pratica Pratica */
            if ($pratica->getGestore()) {
                if (!isset($aggiornamenti[$pratica->getGestore()->getId()])) {
                    $aggiornamenti[$pratica->getGestore()->getId()] = array();
                }
                $aggiornamenti[$pratica->getGestore()->getId()][] = $pratica;
            }
        }
        foreach ($cliente->getUtenze() as $gestore) {
            /* @var $gestore \JF\ACLBundle\Entity\Gestore */
            if ($gestore->hasRole('C_ADMIN')) {
                $this->notify($gestore, 'Aggiornamenti personali BDX Contec', 'ClaimsContecBundle:email:aggiornamentiAdmin', array('pratiche_nuove' => $pratiche_nuove, 'pratiche_aggiornate' => $pratiche_aggiornate));
            }
            if (isset($aggiornamenti[$gestore->getId()])) {
                $this->notify($gestore, 'Aggiornamenti generali BDX Contec per ' . $gestore->getNome(), 'ClaimsContecBundle:email:aggiornamentiGestore', array('pratiche' => $aggiornamenti[$gestore->getId()]));
            }
        }

        return array('pratiche_aggiornate' => $pratiche_aggiornate, 'pratiche_nuove' => $pratiche_nuove);
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-import-calendario/{slug}", name="contec_import_calendario_manuale", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Template("ClaimsHBundle:Tabellone:pratica/calendario.html.twig")
     */
    public function calendarioAction($slug) {
        $colonne = array('data', 'autore', 'titolo', 'note');
        set_time_limit(3600);
        $em = $this->getEm();
        $conn = $em->getConnection();
        $csv = $this->getRequest()->get('import');
        $entity = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $entity Pratica */
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }

        $cal = $this->getCalendar();
        $righe = explode("\n", str_replace(array("\r", "\n\n"), array("\n", "\n"), $csv));
        foreach ($righe as $riga) {
            $dati = explode("\t", $riga);
            if (count($dati) >= 4) {
                try {
                    $conn->beginTransaction();
                    $data = \DateTime::createFromFormat('d/m/Y', substr($dati[0], 0, 10));
                    /* @var $data \DateTime */
                    $evento = $this->newEvento($this->JWEB, $entity, $dati[2], $dati[3] . ($dati[1] ? "({$dati[1]})" : ''));
                    $evento->setDataOra($data);
                    $olds = $this->findBy('ClaimsHBundle:Evento', array(
                        'calendario' => $cal->getId(),
                        'tipo' => $evento->getTipo()->getId(),
                        'pratica' => $entity->getId(),
                        'titolo' => $evento->getTitolo(),
                        'note' => $evento->getNote(),
                    ));
//                    Debug::vd($old);
                    if (!$olds) {
                        $em->persist($evento);
                        $em->flush();
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

                    $conn->commit();
                } catch (\Exception $e) {
                    $conn->rollback();
                    throw $e;
                }
            }
        }
        return array('entity' => $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug)));
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-pratica/{slug}", name="contec_import_pratica", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     */
    public function praticaAction($slug) {
        set_time_limit(3600);
        $cliente = $this->getUser()->getCliente();
        $dati = $cliente->getDati();
        if (isset($dati['cl_h_contec-import'])) {
            $sistema = $this->findOneBy('ClaimsHBundle:Sistema', array('nome' => 'Contec'));
            /* @var $sistema \Claims\HBundle\Entity\Sistema */
            $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
            /* @var $sistema \Claims\HBundle\Entity\Pratica */
            list($cookies, $reqTime) = $this->login($sistema, $dati['cl_h_contec-import']);
            $this->enterScheda($sistema, $pratica, $cookies, $reqTime, true);
            $this->logout($sistema, $cookies);
        }
        return $this->redirect($this->generateUrl('claims_hospital_pratica', array('slug' => $pratica->getSlug())));
    }

    private function login(Sistema $sistema, $dati) {
        $get = array(
            'localaCode=it',
        );

        // https://romolo.contec.it/jbrows/plugins/contec/pRouter.jsp?localeCode=it
        $access = $this->curlGet($sistema->getUrlBase() . 'jbrows/plugins/contec/pRouter.jsp?' . implode('&', $get), array('show' => 1));

        $matchs = $cookies = array();
        preg_match_all('/Set-Cookie:[^;]+;/', $access, $matchs);

        foreach ($matchs[0] as $match) {
            $cookies[] = trim(str_replace(array('Set-Cookie:', ';'), array('', ''), $match));
        }
        $get = array(
            'actionRequired=' . 'accesso',
            'userIdJB=' . $dati['username'],
            'userPasswordJB=' . $dati['password'],
            'Entra=' . 'Entra',
        );
        // https://romolo.contec.it/jbrows/plugins/contec/pRouter.jsp?actionRequired=accesso&userIdJB=carlesi&userPasswordJB=al31l1run&Entra=Entra
        //sleep(1);
        $access = $this->curlGet($sistema->getUrlBase() . 'jbrows/plugins/contec/pRouter.jsp?' . implode('&', $get), array('cookies' => $cookies, 'show' => 1));

        $url = $this->getUrlMedicalClaims($access);


        // url ricavato da pagina 
        //sleep(1);
        $tmp = $this->curlGet($url, array('cookies' => $cookies, 'show' => 1));
        $reqTime1 = $this->getReqTime($tmp);

        $matchs = $cookies = array();
        preg_match_all('/Set-Cookie:[^;]+;/', $tmp, $matchs);

        foreach ($matchs[0] as $match) {
            $cookies[] = trim(str_replace(array('Set-Cookie:', ';'), array('', ''), $match));
        }

        //https://romolo.contec.it/jwcmedmal/home/top.jsp?timeReq=1383398697899
        //https://romolo.contec.it/jwcmedmal/home/left.jsp?timeReq=1383398697899
        //https://romolo.contec.it/jwcmedmal/home/menu.jsp?timeReq=1383398697899 *
        //https://romolo.contec.it/jwcmedmal/home/riepiloghiHome.jsp
        //https://romolo.contec.it/jwcmedmal/common/keepAlive.jsp
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/top.jsp?' . implode('&', $reqTime1), array('cookies' => $cookies, 'show' => 1));
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/left.jsp?' . implode('&', $reqTime1), array('cookies' => $cookies));
        $menu = $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/menu.jsp?' . implode('&', $reqTime1), array('cookies' => $cookies));
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/riepiloghiHome.jsp', array('cookies' => $cookies));
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/common/keepAlive.jsp', array('cookies' => $cookies));

        $reqTime = $this->getReqTime($menu);

        return array($cookies, $reqTime);
    }

    private function logout(Sistema $sistema, $cookies) {
        $get = array(
            'actionRequired=' . 'logout',
            'Esci=' . 'Esci',
        );
        //sleep(1);
        $logout = $this->curlGet($sistema->getUrlBase() . 'jbrows/plugins/contec/pRouter.jsp?' . implode('&', $get), array('cookies' => $cookies));
    }

    private function enterBdx($dati) {

        $sistema = $this->findOneBy('ClaimsHBundle:Sistema', array('nome' => 'Contec'));
        /* @var $sistema \Claims\HBundle\Entity\Sistema */
        list($cookies, $reqTime) = $this->login($sistema, $dati);

        $get = array(
            'contesto=' . 'jobmanager',
            $reqTime[0],
        );

        //https://romolo.contec.it/jwcmedmal/home/main.jsp?contesto=jobmanager&timeReq=1383399106882
        //sleep(1);
        $jobman = $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/main.jsp?' . implode('&', $get), array('cookies' => $cookies));
        $reqTime1 = $this->getReqTime($jobman);

        $get = array(
            'action=' . '',
            'parameters=' . '',
            $reqTime1[0],
        );

        //https://romolo.contec.it/jwcmedmal/home/top.jsp?timeReq=1383398697899
        //https://romolo.contec.it/jwcmedmal/home/left.jsp?timeReq=1383398697899 *
        //https://romolo.contec.it/jwcmedmal/home/menu.jsp?timeReq=1383398697899
        //https://romolo.contec.it/jwcmedmal/home/empty.htm?action=&parameters=&timeReq=1383413348586
        //https://romolo.contec.it/jwcmedmal/common/keepAlive.jsp
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/top.jsp?' . implode('&', $reqTime1), array('cookies' => $cookies, 'show' => 1));
        $menu = $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/left.jsp?' . implode('&', $reqTime1), array('cookies' => $cookies));
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/menu.jsp?' . implode('&', $reqTime1), array('cookies' => $cookies));
//        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/empty.jsp?' . implode('&', $p), array('cookies' => $cookies));
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/common/keepAlive.jsp', array('cookies' => $cookies));


        $get = array(
            'to=' . '/environment/MenuHC?actionRequired=|@PROMPT|led.jobmanager.Job|Gestione%20Job|level02|frm.JobManager|/common/mainFrame.jsp|getDefaultPager|led.jobmanager.Job_PAGER|led.jobmanager.JobSearchBean|led.jobmanager.JobSearchBean|true|',
            'label=' . 'Gestione+job',
        );
        $post = array(
            'parameters=' . '',
            'uy=' . '',
            'intestatario=' . '',
            'polizza=' . '',
            'sinistro=' . '',
            'riferimento=' . '',
        );
        //sleep(1);
        // https://romolo.contec.it/jwcmedmal/home/forwarder.jsp?to=/environment/MenuHC?actionRequired=|@PROMPT|led.jobmanager.Job|Gestione%20Job|level02|frm.JobManager|/common/mainFrame.jsp|getDefaultPager|led.jobmanager.Job_PAGER|led.jobmanager.JobSearchBean|led.jobmanager.JobSearchBean|true|&label=Gestione+job
        $elenco = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/home/forwarder.jsp?' . implode('&', $get), $post, array('cookies' => $cookies, 'show' => 1));
        $jobId = $this->getJobId($elenco);

        $post = array(
            'dirty=' . '-1',
            'actionRequired=' . '%7C%40VIEW%7Cled.jobmanager.Job%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Job%7Cfrm.dtl.Job%7C%2Fcommon%2FmainFrame.jsp%7CpagerTable%7Csrcfrm.Job%7CSINGLE_OBJECT%7C',
            'formClass=' . 'frm.JobManager',
            'action=' . '',
            'listWaitingJobs_jobId_0=' . $jobId,
            'pagerTable_page=' . '1',
            '#SEL__pagerTable0=' . 'on',
            'pagerTable_cmdView_0=' . 'Visualizza',
            'formComponentsIDsList_frm.JobManager=' . '%23%23revert_to_original_readform%23%23',
        );
        //sleep(1);
        // https://romolo.contec.it/jwcmedmal/jobmanager/JobManagerHC
        // dirty=-1&actionRequired=%7C%40VIEW%7Cled.jobmanager.Job%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Job%7Cfrm.dtl.Job%7C%2Fcommon%2FmainFrame.jsp%7CpagerTable%7Csrcfrm.Job%7CSINGLE_OBJECT%7C&formClass=frm.JobManager&action=&listWaitingJobs_jobId_0=9835&pagerTable_page=1&%23SEL__pagerTable0=on&pagerTable_cmdView_0=Visualizza&formComponentsIDsList_frm.JobManager=%23%23revert_to_original_readform%23%23
        $pagina = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/jobmanager/JobManagerHC', implode('&', $post), array('cookies' => $cookies));

        $post = array(
            'dirty=' . '0',
            'actionRequired=' . '%7C%40VIEWDEPENDENT%7Cled.jobmanager.Report%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Report%7Cfrm.edit.reports%7C%2Fcommon%2FmainFrame.jsp%7Creports%7Cfrm.dtl.Job%7C',
            'formClass=' . 'frm.dtl.Job',
            'action=' . '',
            '#SEL__reports3=' . 'on',
            'reports_cmdView.reports_3=' . 'Visualizza',
            'formComponentsIDsList_frm.dtl.Job=' . 'brd_entity_start%7Cpid%7Ctitle%7CactualState%7CexecutionMode%7CconcurrentMode%7CprocessClass%7CprocessParameterString%7Cbrd_entity_end%7CcmdCancel%7Cbrd_users_start%7Cusers%7Cbrd_users_end%7Cbrd_reports_start%7Creports%7Cbrd_reports_end%7Cbrd_states_start%7Cstates%7Cbrd_states_end%7Cbrd_schedulers_start%7Cschedulers%7Cbrd_schedulers_end%7C',
        );
        //sleep(1);
        // https://romolo.contec.it/jwcmedmal/jobmanager/JobManagerHC
        // dirty=0&actionRequired=%7C%40VIEWDEPENDENT%7Cled.jobmanager.Report%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Report%7Cfrm.edit.reports%7C%2Fcommon%2FmainFrame.jsp%7Creports%7Cfrm.dtl.Job%7C&formClass=frm.dtl.Job&action=&%23SEL__reports3=on&reports_cmdView.reports_3=Visualizza&formComponentsIDsList_frm.dtl.Job=brd_entity_start%7Cpid%7Ctitle%7CactualState%7CexecutionMode%7CconcurrentMode%7CprocessClass%7CprocessParameterString%7Cbrd_entity_end%7CcmdCancel%7Cbrd_users_start%7Cusers%7Cbrd_users_end%7Cbrd_reports_start%7Creports%7Cbrd_reports_end%7Cbrd_states_start%7Cstates%7Cbrd_states_end%7Cbrd_schedulers_start%7Cschedulers%7Cbrd_schedulers_end%7C
        $dettxls = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/jobmanager/JobManagerHC', implode('&', $post), array('cookies' => $cookies));

        $post = array(
            'dirty=' . '0',
            'actionRequired=' . 'led.jobmanager.Detail.download',
            'formClass=' . 'frm.edit.reports',
            'action=' . '',
            'name=' . 'Report+Bordero\'+Status+-+Globale+per+stato+sinistr',
            'cmdCancel=' . 'Indietro',
            '#SEL__details0=' . 'on',
            'details_cmdDownload_0=' . 'Scarica',
            'formComponentsIDsList_frm=' . 'brd_entity_start%7Cname%7Cdescription%7Cbrd_entity_end%7CcmdCancel%7Cbrd_details_start%7Cdetails%7Cbrd_details_end%7C',
        );
        //sleep(1);
        // https://romolo.contec.it/jwcmedmal/jobmanager/JobManagerHC
        // dirty=0&actionRequired=led.jobmanager.Detail.download&formClass=frm.edit.reports&action=&name=Report+Bordero'+Status+-+Globale+per+stato+sinistr&cmdCancel=Indietro&%23SEL__details0=on&details_cmdDownload_0=Scarica&formComponentsIDsList_frm.edit.reports=brd_entity_start%7Cname%7Cdescription%7Cbrd_entity_end%7CcmdCancel%7Cbrd_details_start%7Cdetails%7Cbrd_details_end%7C
        $xls = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/jobmanager/JobManagerHC', implode('&', $post), array('cookies' => $cookies));

        $this->logout($sistema, $cookies);

        return $xls;
    }

    private function enterScheda(Sistema $sistema, Pratica $pratica, $cookies, $reqTime, $pulizia = true) {
        try {
            $this->getEm()->beginTransaction();
            $get = array(
                'to=' . '/sinistri/SinistriRouter',
                'action=' . 'sinistro',
                'parameters=' . 'menu',
                'label=' . 'Sinistri',
            );
            $post = array(
                'parameters=' . '',
                'uy=' . '',
                'intestatario=' . '',
                'polizza=' . '',
                'sinistro=' . '',
                'riferimento=' . '',
            );
            //sleep(1);
            // https://romolo.contec.it/jwcmedmal/home/forwarder.jsp?to=/sinistri/SinistriRouter&action=sinistro&parameters=menu&label=Sinistri
            $form = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/home/forwarder.jsp?' . implode('&', $get), $post, array('cookies' => $cookies, 'show' => 1));

            $post = array(
                'form_name=' . 'ricercaSinistroNuova.jsp',
                'statoPerizia=' . '',
                'dataSinDa=' . '',
                'dataSinA=' . '',
                'idCompagnia=' . '0',
                'idBroker=' . '0',
                'idPolizza=' . '0',
                'numeroSinistro=' . $pratica->getCodice(),
                'rifIntermediario=' . '',
                'rifCliente=' . str_replace('  ', ' ', $pratica->getClaimant()),
                'rifCompagnia=' . '',
                'altroRiferimento=' . '',
                'reopened=' . 'N',
                'altriDati=' . '',
                'action=' . 'ricercaSinistri',
                'parameters=' . '',
                'button=' . '1',
                'ricercaSinistri=' . 'Effettua Ricerca',
                'tipoDenuncia=' . 'danniAPersoneMedMal',
            );
            //sleep(1);
            // https://romolo.contec.it/jwcmedmal/sinistri/SinistriRouter
            // form_name=ricercaSinistroNuova.jsp&statoPerizia=&dataSinDa=&dataSinA=&idCompagnia=0&idBroker=0&idPolizza=0&numeroSinistro=cgl%2F07%2F3&rifIntermediario=&rifCliente=usai+francesco&rifCompagnia=&altroRiferimento=&reopened=N&altriDati=&action=ricercaSinistri&parameters=&button=1&ricercaSinistri=Effettua+Ricerca&tipoDenuncia=danniAPersoneMedMal
            $listing = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/sinistri/SinistriRouter', implode('&', $post), array('cookies' => $cookies));

            try {
                $post = array(
                    'risultatiChecked=' . 'on',
                    'action=' . 'schedaTrattazione',
                    'parameters=' . '|0|',
                    'button=' . '1',
                    'action=' . '',
                    'opzioniAction=' . 'Scheda',
                );
                //sleep(1);
                // https://romolo.contec.it/jwcmedmal/jobmanager/JobManagerHC
                // dirty=0&actionRequired=%7C%40VIEWDEPENDENT%7Cled.jobmanager.Report%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Report%7Cfrm.edit.reports%7C%2Fcommon%2FmainFrame.jsp%7Creports%7Cfrm.dtl.Job%7C&formClass=frm.dtl.Job&action=&%23SEL__reports3=on&reports_cmdView.reports_3=Visualizza&formComponentsIDsList_frm.dtl.Job=brd_entity_start%7Cpid%7Ctitle%7CactualState%7CexecutionMode%7CconcurrentMode%7CprocessClass%7CprocessParameterString%7Cbrd_entity_end%7CcmdCancel%7Cbrd_users_start%7Cusers%7Cbrd_users_end%7Cbrd_reports_start%7Creports%7Cbrd_reports_end%7Cbrd_states_start%7Cstates%7Cbrd_states_end%7Cbrd_schedulers_start%7Cschedulers%7Cbrd_schedulers_end%7C
                $scheda = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/sinistri/SinistriRouter', implode('&', $post), array('cookies' => $cookies));

                $datiScheda = $this->findDatiScheda($scheda);

                $pratica->setMedicoLegale(strip_tags($datiScheda['medicoLegale']));
                $pratica->setSpecialista(strip_tags($datiScheda['specialista']));
                $pratica->setPerito(strip_tags($datiScheda['perito']));
                $pratica->setCoDifensore(strip_tags($datiScheda['coDifensore']));
                $pratica->setRivalsista(strip_tags($datiScheda['rivalsista']));

                /*
                 * TODO: MODIFICA DOPO 30 GIORNI DALLA MESSA ONLINE DI QUESTA PARTE 
                 */
                if (!$pratica->getAlignedAt()) {

                    $this->getRepository('ClaimsHBundle:Evento')->cancellaTipoDaPratica($pratica, $this->getTipoEvento($this->JWEB));
                    $this->getRepository('ClaimsHBundle:Evento')->cancellaTipoDaPratica($pratica, $this->getTipoEvento($this->EMAIL_JWEB), null, 'From:%');
                    $this->getRepository('ClaimsHBundle:Evento')->cancellaTipoDaPratica($pratica, $this->getTipoEvento($this->ALL_JWEB));

                    foreach ($datiScheda['eventi'] as $_evento) {
                        if (isset($_evento['comunicazione'])) {
                            //sleep(1);
                            $post = array(
                                'actionRequired=' . 'schedaTrattazioneSinistro.visualizzaComunicazione',
                                'parameters=' . $_evento['comunicazione'],
                                'notaLavorazione=' . '',
                                'dataScadenzaNota=' . '',
                            );
                            // https://romolo.contec.it/jwcmedmal/avvisoDanno/AvvisoDannoHC
                            // actionRequired=schedaTrattazioneSinistro.visualizzaComunicazione&parameters=135527&notaLavorazione=&dataScadenzaNota=
                            $_comunicazione = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/avvisoDanno/AvvisoDannoHC', implode('&', $post), array('cookies' => $cookies));
                            $comunizazione = $this->getComunicazione($_comunicazione);
                            $evento = $this->newEvento($this->EMAIL_JWEB, $pratica, $comunizazione['titolo'], $comunizazione['note'] . "\n <small>(inviato da {$_evento['utente']})</small>");
                            $evento->setDataOra($_evento['data']);
                            $evento->setComunicazione($_evento['comunicazione']);
                            $this->persist($evento);
                        } elseif (isset($_evento['allegato'])) {
                            //sleep(1);
                            // https://romolo.contec.it/jwcmedmal/avvisoDanno/AvvisoDannoHC
                            // actionRequired=schedaTrattazioneSinistro.visualizzaComunicazione&parameters=135527&notaLavorazione=&dataScadenzaNota=
                            $_allegato = $this->datiAllegato($this->curlGet($sistema->getUrlBase() . substr($_evento['allegato'], 1), array('cookies' => $cookies, 'show' => 1)));
                            $url = '/uploads/contec' . $pratica->getCliente()->getId() . '/' . $pratica->getSlug() . '/' . $_evento['allegato_id'] . '/' . $_allegato['filename'];
                            $filepath = __DIR__ . '/../../../../web' . $url;
                            $dir = str_replace('/' . $_allegato['filename'], '', $filepath);
                            if (!file_exists($dir)) {
                                mkdir($dir, 0777, true);
                            }
                            $handle = fopen($filepath, 'w');
                            fwrite($handle, $_allegato['file']);
                            fclose($handle);
                            $evento = $this->newEvento($this->ALL_JWEB, $pratica, 'Documento allegato', $_evento['note'] . '\n' . $_allegato['filename']);
                            $evento->setDataOra($_evento['data']);
                            $evento->setAllegato($_evento['allegato_id']);
                            $evento->setUrl($url);
                            $this->persist($evento);

                            $doc = new Documento();
                            $doc->setEvento($evento);
                            $doc->setAllegato($_evento['allegato_id']);
                            $doc->setMimetype($_allegato['mimetype']);
                            $doc->setSize($_allegato['filesize']);
                            $doc->setTitolo($_evento['note']);
                            $doc->setFilename($_allegato['filename']);
                            $doc->setUrl($url);
                            $this->persist($doc);
                        } else {
                            $evento = $this->newEvento($this->JWEB, $pratica, $_evento['tipo'], $_evento['note'] . ($_evento['utente'] ? "\n({$_evento['utente']})" : ""));
                            $evento->setDataOra($_evento['data']);
                            $this->persist($evento);
                        }
                    }
                } else {
                    if($pulizia) {
                        $this->getRepository('ClaimsHBundle:Evento')->cancellaTipoDaPratica($pratica, $this->getTipoEvento($this->EMAIL_JWEB));
                    } else {
                        $this->getRepository('ClaimsHBundle:Evento')->cancellaTipoDaPratica($pratica, $this->getTipoEvento($this->EMAIL_JWEB), null, 'From:%');
                    }

                    foreach ($datiScheda['eventi'] as $_evento) {
                        if (isset($_evento['comunicazione'])) {
                            if (!$this->findOneBy('ClaimsHBundle:Evento', array('pratica' => $pratica->getId(), 'tipo' => $this->getTipoEvento($this->EMAIL_JWEB)->getId(), 'comunicazione' => $_evento['comunicazione']))) {
                                //sleep(1);
                                $post = array(
                                    'actionRequired=' . 'schedaTrattazioneSinistro.visualizzaComunicazione',
                                    'parameters=' . $_evento['comunicazione'],
                                    'notaLavorazione=' . '',
                                    'dataScadenzaNota=' . '',
                                );
                                // https://romolo.contec.it/jwcmedmal/avvisoDanno/AvvisoDannoHC
                                // actionRequired=schedaTrattazioneSinistro.visualizzaComunicazione&parameters=135527&notaLavorazione=&dataScadenzaNota=
                                $_comunicazione = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/avvisoDanno/AvvisoDannoHC', implode('&', $post), array('cookies' => $cookies));
                                $comunizazione = $this->getComunicazione($_comunicazione);
                                $evento = $this->newEvento($this->EMAIL_JWEB, $pratica, $comunizazione['titolo'], $comunizazione['note'] . "\n <small>(inviato da {$_evento['utente']})</small>");
                                $evento->setDataOra($_evento['data']);
                                $evento->setComunicazione($_evento['comunicazione']);
                                $this->persist($evento);
                            }
                        } elseif (isset($_evento['allegato'])) {
                            if (!$this->findOneBy('ClaimsHBundle:Evento', array('pratica' => $pratica->getId(), 'tipo' => $this->getTipoEvento($this->ALL_JWEB)->getId(), 'allegato' => $_evento['allegato_id']))) {
                                //sleep(1);
                                // https://romolo.contec.it/jwcmedmal/avvisoDanno/AvvisoDannoHC
                                // actionRequired=schedaTrattazioneSinistro.visualizzaComunicazione&parameters=135527&notaLavorazione=&dataScadenzaNota=
                                $_allegato = $this->datiAllegato($this->curlGet($sistema->getUrlBase() . substr($_evento['allegato'], 1), array('cookies' => $cookies, 'show' => 1)));
                                $url = '/uploads/contec' . $pratica->getCliente()->getId() . '/' . $pratica->getSlug() . '/' . $_evento['allegato_id'] . '/' . $_allegato['filename'];
                                $filepath = __DIR__ . '/../../../../web' . $url;
                                $dir = str_replace('/' . $_allegato['filename'], '', $filepath);
                                if (!file_exists($dir)) {
                                    mkdir($dir, 0777, true);
                                }
                                $handle = fopen($filepath, 'w');
                                fwrite($handle, $_allegato['file']);
                                fclose($handle);
                                $evento = $this->newEvento($this->ALL_JWEB, $pratica, 'Documento allegato', $_evento['note'] . '\n' . $_allegato['filename']);
                                $evento->setDataOra($_evento['data']);
                                $evento->setAllegato($_evento['allegato_id']);
                                $evento->setUrl($url);
                                $this->persist($evento);

                                $doc = new Documento();
                                $doc->setEvento($evento);
                                $doc->setAllegato($_evento['allegato_id']);
                                $doc->setMimetype($_allegato['mimetype']);
                                $doc->setSize($_allegato['filesize']);
                                $doc->setTitolo($_evento['note']);
                                $doc->setFilename($_allegato['filename']);
                                $doc->setUrl($url);
                                $this->persist($doc);
                            }
                        } else {
                            $old = $this->findOneBy('ClaimsHBundle:Evento', array(
                                'data_ora' => $_evento['data'],
                                'tipo' => $this->getTipoEvento($this->JWEB)->getId(),
                                'pratica' => $pratica->getId(),
                                'titolo' => $_evento['tipo'],
                                'note' => $_evento['note'] . ($_evento['utente'] ? "\n({$_evento['utente']})" : ""),
                            ));
                            if (!$old) {
                                $evento = $this->newEvento($this->JWEB, $pratica, $_evento['tipo'], $_evento['note'] . ($_evento['utente'] ? "\n({$_evento['utente']})" : ""));
                                $evento->setDataOra($_evento['data']);
                                $this->persist($evento);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                
            }

            $pratica->setAlignedAt(new \DateTime());
            $this->getEm()->commit();
            $this->persist($pratica);
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }

        return $pratica->getSlug();
    }

    private function getUrlMedicalClaims($source) {
        preg_match_all('/window.open\([^\(]+\);/', $source, $m);
        return str_replace(array("window.open('", "');"), array('', ''), $m[0][1]);
    }

    private function getReqTime($source) {
        preg_match('/timeReq=[0-9]+/', $source, $m);
        return $m;
    }

    private function getJobId($source) {
        preg_match('/<input type=\'hidden\' name=\'listWaitingJobs_jobId_0\' value=\'[0-9]+\' \/>/', $source, $m);
        preg_match('/[0-9]{2,9}/', $m[0], $n);
        return $n[0];
    }

    private function findDatiScheda($source) {
        $out = array('eventi' => array());
        $out['medicoLegale'] = $this->findInScheda('Medico legale:', $source);
        $out['specialista'] = $this->findInScheda('Specialista:', $source);
        $out['perito'] = $this->findInScheda('Perito:', $source);
        $out['coDifensore'] = $this->findInScheda('Co-difensore:', $source);
        $out['rivalsista'] = $this->findInScheda('Rivalsista:', $source);
        $pos_start = strpos($source, '<table width="100%" border="0" cellspacing="0" cellpadding="2">', strpos($source, 'Attivit'));
        $pos_end = strpos($source, '</table>', $pos_start) + strlen('</table>');
        $table = '<html><body>' . preg_replace(array('/<table[^>]+>/', '/<tr[^>]+>/', '/<td[^>]+>/', '/\(\);"[^>]*>/'), array('<table>', '<tr>', '<td>', '();">'), substr($source, $pos_start, $pos_end - $pos_start)) . '</body></html>';

        $doc = new \DOMDocument();
        $doc->loadHTML($table);

        $tag_html = Dom::getDOMBase($doc);
        $tag_body = Dom::getDOMElement($tag_html, array('tag' => 'body'));
        $table = Dom::getDOMElement($tag_body, array('tag' => 'table'));
        if ($table) {
            $trs = Dom::getDOMElement($table, array('tag' => 'tr'), false);
            $_evento = array('data', 'utente', 'tipo', 'note');
            foreach ($trs as $tr) {
                /* @var $tr \DOMElement */
                $tds = Dom::getDOMElement($tr, array('tag' => 'td'), false);
                $evento = array();
                foreach ($tds as $i => $td) {
                    /* @var $td \DOMElement */
                    switch ($i) {
                        case 0: //data
                            if (strlen($td->nodeValue) > 10) {
                                $evento[$_evento[$i]] = \DateTime::createFromFormat('d/m/Y H:i:s', $td->nodeValue);
                            } else {
                                $evento[$_evento[$i]] = \DateTime::createFromFormat('d/m/Y H:i:s', $td->nodeValue . ' 08:00:00');
                            }
                            break;
                        case 2:
                            $a = Dom::getDOMElement($td, array('tag' => 'a'));
                            /* @var $a \DOMElement */
                            if ($a) {
                                $evento[$_evento[$i]] = $a->nodeValue;
                                if ($a->nodeValue == 'Comunicazione') {
                                    $evento['comunicazione'] = $this->getComunicazioneAllegatoId($a->getAttribute('href'));
                                }
                                if ($a->nodeValue == 'Documento allegato') {
                                    $evento['allegato'] = $a->getAttribute('href');
                                    $evento['allegato_id'] = $this->getComunicazioneAllegatoId($a->getAttribute('href'));
                                }
                                break;
                            }
                        case 1:
                        case 3:
                            $evento[$_evento[$i]] = $td->nodeValue;
                            break;
                    }
                }
                $out['eventi'][] = $evento;
            }
        }

        return $out;
    }

    private function findInScheda($title, $source) {
        $pos = strpos($source, $title);
        preg_match('/<span class="plateValue">[^<]*<\/span>/', $source, $m, 0, $pos);
        return isset($m[0]) ? $m[0] : '';
    }

    private function getComunicazioneAllegatoId($source) {
        preg_match('/[0-9]+/', $source, $m);
        return $m[0];
    }

    private function getComunicazione($source) {
        preg_match('/formFields\[[0-9]+\]\[0\]="oggetto";/', $source, $o);
        preg_match('/formFields\[[0-9]+\]\[0\]="contenuto";/', $source, $c);
        $orx = '/' . str_replace(array('[', ']'), array('\\[', '\\]'), substr($o[0], 0, strpos($o[0], '[0]'))) . '\[1\]="[^\n]+";/';
        $crx = '/' . str_replace(array('[', ']'), array('\\[', '\\]'), substr($c[0], 0, strpos($c[0], '[0]'))) . '\[1\]="[^\n]+";/';
        $orr = substr($o[0], 0, strpos($o[0], '[0]')) . '[1]="';
        $crr = substr($c[0], 0, strpos($c[0], '[0]')) . '[1]="';
        preg_match($orx, $source, $o1);
        preg_match($crx, $source, $c1);
        $os = str_replace(array('\\n', $orr, '";'), array('\n', '', ''), $o1[0]);
        $cs = str_replace(array('\\n', $crr, '";'), array('\n', '', ''), $c1[0]);
        
//        Debug::vd(array('orx' => $orx, 'crx' => $crx, 'orr' => $orr, 'crr' => $crr, 'o1' => $o1, 'c1' => $c1, 'titolo' => $os, 'note' => $cs));
        return array('titolo' => $os, 'note' => $cs);
    }

    private function datiAllegato($source) {
        $out = array();
        preg_match('/filename=[^\n]+/', $source, $m);
        $out['filename'] = trim(str_replace('filename=', '', $m[0]));
        preg_match('/Content-Type:[^\n]+/', $source, $m);
        $out['mimetype'] = trim(str_replace('Content-Type:', '', $m[0]));
        preg_match('/Content-Length:[^\n]+/', $source, $m);
        $out['filesize'] = trim(str_replace('Content-Length:', '', $m[0]));
        $p = strpos($source, 'Connection: close') + strlen('Connection: close');
        $out['file'] = trim(substr($source, $p));
        return $out;
    }

}

/*

//form
https://romolo.contec.it/jbrows/plugins/contec/pRouter.jsp?localeCode=it

//login 
https://romolo.contec.it/jbrows/plugins/contec/pRouter.jsp?actionRequired=accesso&userIdJB=carlesi&userPasswordJB=al31l1run&Entra=Entra

//medical pratics
https://romolo.contec.it/jwcmedmal/portale/JBGW.jsp?J_REQUEST=028-135-036-235-038-236-030-134-219-086-250-212-119-026-068-122-002-089-044-003-071-218-104-075-249-207-205-163-078-147-081-158-229-022-190-049-192-001-098-113-236-037-068-248-063-101-062-003-175-141-086-227-242-109-196-103-051-235-043-196-195-188-050-173-233-205-219-140-198-142-107-161-069-025-200-214-237-111-045-152-075-065-068-174-018-132-169-097-014-062-210-033-160-232-244-139-152-012-095-167-000-061-123-034-161-140-013-254-040-197-057-153&FINE=1&localeCode=it

//frames
https://romolo.contec.it/jwcmedmal/home/top.jsp?timeReq=1383398697899
https://romolo.contec.it/jwcmedmal/home/left.jsp?timeReq=1383398697899
https://romolo.contec.it/jwcmedmal/home/menu.jsp?timeReq=1383398697899 *
https://romolo.contec.it/jwcmedmal/home/riepiloghiHome.jsp
https://romolo.contec.it/jwcmedmal/common/keepAlive.jsp

//jobmanager
https://romolo.contec.it/jwcmedmal/home/main.jsp?contesto=jobmanager&timeReq=1383399106882

//frames
https://romolo.contec.it/jwcmedmal/home/top.jsp?timeReq=1383398697899
https://romolo.contec.it/jwcmedmal/home/left.jsp?timeReq=1383399169700 *
https://romolo.contec.it/jwcmedmal/home/menu.jsp?timeReq=1383398697899
https://romolo.contec.it/jwcmedmal/home/empty.htm?action=&parameters=&timeReq=1383413348586
https://romolo.contec.it/jwcmedmal/common/keepAlive.jsp

//gestione jobs (post)
https://romolo.contec.it/jwcmedmal/home/forwarder.jsp?to=/environment/MenuHC?actionRequired=|@PROMPT|led.jobmanager.Job|Gestione%20Job|level02|frm.JobManager|/common/mainFrame.jsp|getDefaultPager|led.jobmanager.Job_PAGER|led.jobmanager.JobSearchBean|led.jobmanager.JobSearchBean|true|&label=Gestione+job

//dettaglio job (post)
https://romolo.contec.it/jwcmedmal/jobmanager/JobManagerHC
sì dirty=-1&actionRequired=%7C%40VIEW%7Cled.jobmanager.Job%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Job%7Cfrm.dtl.Job%7C%2Fcommon%2FmainFrame.jsp%7CpagerTable%7Csrcfrm.Job%7CSINGLE_OBJECT%7C&formClass=frm.JobManager&action=&listWaitingJobs_jobId_0=9835&pagerTable_page=1&%23SEL__pagerTable0=on&pagerTable_cmdView_0=Visualizza&formComponentsIDsList_frm.JobManager=%23%23revert_to_original_readform%23%23
no dirty=-1&actionRequired=%7C%40VIEW%7Cled.jobmanager.Job%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Job%7Cfrm.dtl.Job%7C%2Fcommon%2FmainFrame.jsp%7CpagerTable%7Csrcfrm.Job%7CSINGLE_OBJECT%7C&formClass=frm.JobManager&action=&listWaitingJobs_jobId_0=9835&pagerTable_page=1&%23SEL__pagerTable1=on&pagerTable_cmdView_1=Visualizza&formComponentsIDsList_frm.JobManager=%23%23revert_to_original_readform%23%23

//xls page (post)
https://romolo.contec.it/jwcmedmal/jobmanager/JobManagerHC
   dirty=0&actionRequired=%7C%40VIEWDEPENDENT%7Cled.jobmanager.Report%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Report%7Cfrm.edit.reports%7C%2Fcommon%2FmainFrame.jsp%7Creports%7Cfrm.dtl.Job%7C&formClass=frm.dtl.Job&action=&%23SEL__reports3=on&reports_cmdView.reports_3=Visualizza&formComponentsIDsList_frm.dtl.Job=brd_entity_start%7Cpid%7Ctitle%7CactualState%7CexecutionMode%7CconcurrentMode%7CprocessClass%7CprocessParameterString%7Cbrd_entity_end%7CcmdCancel%7Cbrd_users_start%7Cusers%7Cbrd_users_end%7Cbrd_reports_start%7Creports%7Cbrd_reports_end%7Cbrd_states_start%7Cstates%7Cbrd_states_end%7Cbrd_schedulers_start%7Cschedulers%7Cbrd_schedulers_end%7C

//xls file (post)
https://romolo.contec.it/jwcmedmal/jobmanager/JobManagerHC
   dirty=0&actionRequired=led.jobmanager.Detail.download&formClass=frm.edit.reports&action=&name=Report+Bordero'+Status+-+Globale+per+stato+sinistr&cmdCancel=Indietro&%23SEL__details0=on&details_cmdDownload_0=Scarica&formComponentsIDsList_frm.edit.reports=brd_entity_start%7Cname%7Cdescription%7Cbrd_entity_end%7CcmdCancel%7Cbrd_details_start%7Cdetails%7Cbrd_details_end%7C

//logout
https://romolo.contec.it/jbrows/plugins/contec/pRouter.jsp?actionRequired=logout&Esci=Esci




//SINISTRI (post)
https://romolo.contec.it/jwcmedmal/home/forwarder.jsp?to=/sinistri/SinistriRouter&action=sinistro&parameters=menu&label=Sinistri

//RICERCA (post)
https://romolo.contec.it/jwcmedmal/sinistri/SinistriRouter  
   form_name=ricercaSinistroNuova.jsp&statoPerizia=&dataSinDa=&dataSinA=&idCompagnia=0&idBroker=0&idPolizza=0&numeroSinistro=cgl%2F07%2F3&rifIntermediario=&rifCliente=usai+francesco&rifCompagnia=&altroRiferimento=&reopened=N&altriDati=&action=ricercaSinistri&parameters=&button=1&ricercaSinistri=Effettua+Ricerca&tipoDenuncia=danniAPersoneMedMal

//SCHEDA (post)
https://romolo.contec.it/jwcmedmal/sinistri/SinistriRouter
   risultatiChecked=on&action=schedaTrattazione&parameters=%7C0%7C&button=1&opzioniAction=Scheda

//Dettagli (post)
https://romolo.contec.it/jwcmedmal/avvisoDanno/AvvisoDannoHC
   actionRequired=schedaTrattazioneSinistro.visualizzaComunicazione&parameters=135527&notaLavorazione=&dataScadenzaNota=

//Allegati
https://romolo.contec.it/jwcmedmal/servizi/ScaricaDocumentoGED?id=32039


 
 */