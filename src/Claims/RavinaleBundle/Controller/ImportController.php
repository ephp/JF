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
        \Claims\HBundle\Controller\Traits\CalendarController,
        \Claims\HBundle\Controller\Traits\ImportController;

    /**
     * @Route("-manuale", name="ravinale_import_manuale")
     * @Template()
     */
    public function manualeAction() {
        set_time_limit(3600);
        $cliente = $this->getUser()->getCliente();
        $dati = $cliente->getDati();
        $logs = array();
        if (isset($dati['cl_h_ravinale-import'])) {
            $bdxs = $this->enterBdx($dati['cl_h_ravinale-import']);
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
     * @Route("-cron", name="ravinale_import_cron", defaults={"_format": "json"})
     * @Template()
     */
    public function cronAction() {
        $out = array();
        foreach ($this->findAll('JFACLBundle:Cliente') as $cliente) {
            //$cliente = $this->getUser()->getCliente();
            /* @var $cliente \JF\ACLBundle\Entity\Cliente */
            set_time_limit(3600);
            $dati = $cliente->getDati();
            $logs = array();
            if (isset($dati['cl_h_ravinale-import'])) {
                $bdxs = $this->enterBdx($dati['cl_h_ravinale-import']);
                foreach ($bdxs as $bdx) {
//                    Debug::pr('-----------', true);
                    $logs[] = $this->importBdx($cliente, $bdx);
                }
                $out[$cliente->getId()] = array(
                    'pratiche_nuove' => array(),
                    'pratiche_aggiornate' => array(),
                    'pratiche_invariate' => array(),
                );
                foreach ($logs as $log) {
                    $out[$cliente->getId()]['pratiche_nuove'] = array_merge($out[$cliente->getId()]['pratiche_nuove'], $log['pratiche_nuove']);
                    $out[$cliente->getId()]['pratiche_aggiornate'] = array_merge($out[$cliente->getId()]['pratiche_aggiornate'], $log['pratiche_aggiornate']);
                    $out[$cliente->getId()]['pratiche_invariate'] = array_merge($out[$cliente->getId()]['pratiche_invariate'], $log['pratiche_invariate']);
                }
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

    private function enterBdx($dati) {
        $admin = $this->find('JFACLBundle:Gestore', 1);
        $sistema = $this->findOneBy('ClaimsHBundle:Sistema', array('nome' => 'Ravinale'));
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
//        $this->notify($admin, 'login', 'ClaimsRavinaleBundle:email:pagina', array('html' => $access));

        $matchs = $cookies = array();
        preg_match_all('/Set-Cookie:[^;]+;/', $access, $matchs);

        foreach ($matchs[0] as $match) {
            $cookies[] = trim(str_replace(array('Set-Cookie:', ';'), array('', ''), $match));
        }
        //https://sistema.ravinalepartners.com/Moduli/SanPie2014/EsportaInExcelNewLineNuovo/
        sleep(rand(3, 6));
        $tmp = $this->curlGet($sistema->getUrlBase() . '/Moduli/SanPie2014/EsportaInExcelNewLineNuovo/', array('cookies' => $cookies));
        $out[] = $tmp;
        //https://sistema.ravinalepartners.com/Moduli/SanPie2013/EsportaInExcelNewLineNuovo/
        sleep(rand(3, 6));
        $tmp = $this->curlGet($sistema->getUrlBase() . '/Moduli/SanPie2013/EsportaInExcelNewLineNuovo/', array('cookies' => $cookies));
        $out[] = $tmp;
        //https://sistema.ravinalepartners.com/Moduli/San1/EsportaInExcelNewLineNuovo/
        sleep(rand(3, 6));
        $tmp = $this->curlGet($sistema->getUrlBase() . '/Moduli/San1/EsportaInExcelNewLineNuovo/', array('cookies' => $cookies));
        $out[] = $tmp;
//        $this->notify($admin, '2014 xls', 'ClaimsRavinaleBundle:email:pagina', array('html' => $tmp));
        //https://sistema.ravinalepartners.com/Uscita/
        sleep(rand(3, 6));
        $tmp = $this->curlGet($sistema->getUrlBase() . '/Uscita/', array('cookies' => $cookies));
//        $this->notify($admin, 'logout', 'ClaimsRavinaleBundle:email:pagina', array('html' => $tmp));

        return $out;
    }

    private function importBdx(\JF\ACLBundle\Entity\Cliente $cliente, $source) {
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
        $pratiche_aggiornate = $pratiche_nuove = $pratiche_invariate = array();
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
                    $this->salvaPratica($cliente, $pratica, $pratiche_aggiornate, $pratiche_nuove, $pratiche_invariate);
                    $this->getEm()->commit();
                } catch (\Exception $e) {
                    $this->getEm()->rollback();
                    throw $e;
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
                $this->notify($gestore, 'Aggiornamenti personali BDX Ravinale', 'ClaimsRavinaleBundle:email:aggiornamentiAdmin', array('pratiche_nuove' => $pratiche_nuove, 'pratiche_aggiornate' => $pratiche_aggiornate));
            }
            if (isset($aggiornamenti[$gestore->getId()])) {
                $this->notify($gestore, 'Aggiornamenti generali BDX Ravinale', 'ClaimsRavinaleBundle:email:aggiornamentiGestore', array('pratiche' => $aggiornamenti[$gestore->getId()]));
            }
        }

        return array('pratiche_nuove' => $pratiche_nuove, 'pratiche_aggiornate' => $pratiche_aggiornate);
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-import-calendario/{slug}", name="ravinale_import_calendario_manuale", options={"expose": true, "ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
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
