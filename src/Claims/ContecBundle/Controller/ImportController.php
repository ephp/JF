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
        $out = array();
        if (isset($dati['cl_h_contec-import'])) {
            $bdx = $this->enterBdx($dati['cl_h_contec-import']);
            $source = __DIR__ . '/../../../../web/uploads/contec' . $cliente->getId() . '.xls';
            if(file_exists($source)) {
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
        $get = array(
            'localaCode=it',
        );


        $out = array();

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
        sleep(rand(3, 6));
        $access = $this->curlGet($sistema->getUrlBase() . 'jbrows/plugins/contec/pRouter.jsp?' . implode('&', $get), array('cookies' => $cookies, 'show' => 1));

        $url = $this->getUrlMedicalClaims($access);
        

        // url ricavato da pagina 
        sleep(rand(3, 6));
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
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/top.jsp?' . implode('&', $reqTime1), array('cookies' => $cookies,'show' => 1));
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/left.jsp?' . implode('&', $reqTime1), array('cookies' => $cookies));
        $menu = $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/menu.jsp?' . implode('&', $reqTime1), array('cookies' => $cookies));
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/riepiloghiHome.jsp', array('cookies' => $cookies));
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/common/keepAlive.jsp', array('cookies' => $cookies));
        
        $reqTime2 = $this->getReqTime($menu);
               
         $get = array(
            'contesto=' . 'jobmanager',
            $reqTime2[0],
        );
        
        //https://romolo.contec.it/jwcmedmal/home/main.jsp?contesto=jobmanager&timeReq=1383399106882
        sleep(rand(3, 6));
        $jobman = $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/main.jsp?' . implode('&', $get), array('cookies' => $cookies));
        $reqTime3 = $this->getReqTime($jobman);
        
        $get = array(
            'action=' . '',
            'parameters=' . '',
            $reqTime3[0],
        );
        
        //https://romolo.contec.it/jwcmedmal/home/top.jsp?timeReq=1383398697899
        //https://romolo.contec.it/jwcmedmal/home/left.jsp?timeReq=1383398697899 *
        //https://romolo.contec.it/jwcmedmal/home/menu.jsp?timeReq=1383398697899
        //https://romolo.contec.it/jwcmedmal/home/empty.htm?action=&parameters=&timeReq=1383413348586
        //https://romolo.contec.it/jwcmedmal/common/keepAlive.jsp
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/top.jsp?' . implode('&', $reqTime3), array('cookies' => $cookies,'show' => 1));
        $left = $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/left.jsp?' . implode('&', $reqTime3), array('cookies' => $cookies));
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/menu.jsp?' . implode('&', $reqTime3), array('cookies' => $cookies));
//        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/home/empty.jsp?' . implode('&', $p), array('cookies' => $cookies));
        $this->curlGet($sistema->getUrlBase() . 'jwcmedmal/common/keepAlive.jsp', array('cookies' => $cookies));
        
        
        $get = array(
            'to=' . '/environment/MenuHC?actionRequired=|@PROMPT|led.jobmanager.Job|Gestione%20Job|level02|frm.JobManager|/common/mainFrame.jsp|getDefaultPager|led.jobmanager.Job_PAGER|led.jobmanager.JobSearchBean|led.jobmanager.JobSearchBean|true|',
            'label=' . 'Gestione+job',
        );
        $post = array(
            'parameters='.'',
            'uy='.'',
            'intestatario='.'',
            'polizza='.'',
            'sinistro='.'',
            'riferimento='.'',
        );
        sleep(rand(3, 6));
        // https://romolo.contec.it/jwcmedmal/home/forwarder.jsp?to=/environment/MenuHC?actionRequired=|@PROMPT|led.jobmanager.Job|Gestione%20Job|level02|frm.JobManager|/common/mainFrame.jsp|getDefaultPager|led.jobmanager.Job_PAGER|led.jobmanager.JobSearchBean|led.jobmanager.JobSearchBean|true|&label=Gestione+job
        $elenco = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/home/forwarder.jsp?' . implode('&', $get), $post, array('cookies' => $cookies, 'show' => 1));
        $jobId = $this->getJobId($elenco);
                
        $post = array(
            'dirty='.'-1',
            'actionRequired='.'%7C%40VIEW%7Cled.jobmanager.Job%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Job%7Cfrm.dtl.Job%7C%2Fcommon%2FmainFrame.jsp%7CpagerTable%7Csrcfrm.Job%7CSINGLE_OBJECT%7C',
            'formClass='.'frm.JobManager',
            'action='.'',
            'listWaitingJobs_jobId_0='.$jobId,
            'pagerTable_page='.'1',
            '#SEL__pagerTable0='.'on',
            'pagerTable_cmdView_0='.'Visualizza',
            'formComponentsIDsList_frm.JobManager='.'%23%23revert_to_original_readform%23%23',
        );
        sleep(rand(3, 6));
        // https://romolo.contec.it/jwcmedmal/jobmanager/JobManagerHC
        // dirty=-1&actionRequired=%7C%40VIEW%7Cled.jobmanager.Job%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Job%7Cfrm.dtl.Job%7C%2Fcommon%2FmainFrame.jsp%7CpagerTable%7Csrcfrm.Job%7CSINGLE_OBJECT%7C&formClass=frm.JobManager&action=&listWaitingJobs_jobId_0=9835&pagerTable_page=1&%23SEL__pagerTable0=on&pagerTable_cmdView_0=Visualizza&formComponentsIDsList_frm.JobManager=%23%23revert_to_original_readform%23%23
        $pagina = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/jobmanager/JobManagerHC', implode('&', $post), array('cookies' => $cookies));

        $post = array(
            'dirty='.'0',
            'actionRequired='.'%7C%40VIEWDEPENDENT%7Cled.jobmanager.Report%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Report%7Cfrm.edit.reports%7C%2Fcommon%2FmainFrame.jsp%7Creports%7Cfrm.dtl.Job%7C',
            'formClass='.'frm.dtl.Job',
            'action='.'',
            '#SEL__reports3='.'on',
            'reports_cmdView.reports_3='.'Visualizza',
            'formComponentsIDsList_frm.dtl.Job='.'brd_entity_start%7Cpid%7Ctitle%7CactualState%7CexecutionMode%7CconcurrentMode%7CprocessClass%7CprocessParameterString%7Cbrd_entity_end%7CcmdCancel%7Cbrd_users_start%7Cusers%7Cbrd_users_end%7Cbrd_reports_start%7Creports%7Cbrd_reports_end%7Cbrd_states_start%7Cstates%7Cbrd_states_end%7Cbrd_schedulers_start%7Cschedulers%7Cbrd_schedulers_end%7C',
        );
        sleep(rand(3, 6));
        // https://romolo.contec.it/jwcmedmal/jobmanager/JobManagerHC
        // dirty=0&actionRequired=%7C%40VIEWDEPENDENT%7Cled.jobmanager.Report%7C%40%28WRAPPER_LABEL%23title%29%7Cview.led.jobmanager.Report%7Cfrm.edit.reports%7C%2Fcommon%2FmainFrame.jsp%7Creports%7Cfrm.dtl.Job%7C&formClass=frm.dtl.Job&action=&%23SEL__reports3=on&reports_cmdView.reports_3=Visualizza&formComponentsIDsList_frm.dtl.Job=brd_entity_start%7Cpid%7Ctitle%7CactualState%7CexecutionMode%7CconcurrentMode%7CprocessClass%7CprocessParameterString%7Cbrd_entity_end%7CcmdCancel%7Cbrd_users_start%7Cusers%7Cbrd_users_end%7Cbrd_reports_start%7Creports%7Cbrd_reports_end%7Cbrd_states_start%7Cstates%7Cbrd_states_end%7Cbrd_schedulers_start%7Cschedulers%7Cbrd_schedulers_end%7C
        $dettxls = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/jobmanager/JobManagerHC', implode('&', $post), array('cookies' => $cookies));
        
        $post = array(
            'dirty='.'0',
            'actionRequired='.'led.jobmanager.Detail.download',
            'formClass='.'frm.edit.reports',
            'action='.'',
            'name='.'Report+Bordero\'+Status+-+Globale+per+stato+sinistr',
            'cmdCancel='.'Indietro',
            '#SEL__details0='.'on',
            'details_cmdDownload_0='.'Scarica',
            'formComponentsIDsList_frm='.'brd_entity_start%7Cname%7Cdescription%7Cbrd_entity_end%7CcmdCancel%7Cbrd_details_start%7Cdetails%7Cbrd_details_end%7C',
        );
        sleep(rand(3, 6));
        // https://romolo.contec.it/jwcmedmal/jobmanager/JobManagerHC
        // dirty=0&actionRequired=led.jobmanager.Detail.download&formClass=frm.edit.reports&action=&name=Report+Bordero'+Status+-+Globale+per+stato+sinistr&cmdCancel=Indietro&%23SEL__details0=on&details_cmdDownload_0=Scarica&formComponentsIDsList_frm.edit.reports=brd_entity_start%7Cname%7Cdescription%7Cbrd_entity_end%7CcmdCancel%7Cbrd_details_start%7Cdetails%7Cbrd_details_end%7C
        $xls = $this->curlPost($sistema->getUrlBase() . 'jwcmedmal/jobmanager/JobManagerHC', implode('&', $post), array('cookies' => $cookies));
        
        $get = array(
            'actionRequired=' . 'logout',
            'Esci=' . 'Esci',
        );
        sleep(rand(3, 6));
        $logout = $this->curlGet($sistema->getUrlBase() . 'jbrows/plugins/contec/pRouter.jsp?' . implode('&', $get), array('cookies' => $cookies));
        
        return $xls;
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

    private function importBdx(\JF\ACLBundle\Entity\Cliente $cliente, $source) {
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
                $this->notify($gestore, 'Aggiornamenti generali BDX Contec per '.$gestore->getNome(), 'ClaimsContecBundle:email:aggiornamentiGestore', array('pratiche' => $aggiornamenti[$gestore->getId()]));
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

 */