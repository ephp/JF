<?php

namespace SLC\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;
use Ephp\UtilityBundle\Utility\String;

/**
 * @Route("/slc/claims-hospital-xls")
 */
class XlsController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController,
        \Claims\HBundle\Controller\Traits\TabelloneController,
        Traits\TabelloneController;

    /**
     * @Route("/{monthly_report}",                 name="claims_hospital_xls",               defaults={"monthly_report": false, "mode": "default"},       options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-personale/{monthly_report}",       name="claims_hospital_personale_xls",     defaults={"monthly_report": false, "mode": "personale"},     options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-chiusi/{monthly_report}",          name="claims_hospital_chiuso_xls",        defaults={"monthly_report": false, "mode": "chiuso"},        options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-tutti/{monthly_report}",           name="claims_hospital_tutti_xls",         defaults={"monthly_report": false, "mode": "tutti"},         options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-aperti/{monthly_report}",          name="claims_hospital_aperti_xls",        defaults={"monthly_report": false, "mode": "aperti"},        options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-chiusi-completo/{monthly_report}", name="claims_hospital_chiusi_xls",        defaults={"monthly_report": false, "mode": "chiusi"},        options={"ACL": {"in_role": {"C_ADMIN", "C_RECUPERI_H"}}})
     * @Route("-completo/{monthly_report}",        name="claims_hospital_completo_xls",      defaults={"monthly_report": false, "mode": "completo"},      options={"ACL": {"in_role": {"C_ADMIN", "C_RECUPERI_H"}}})
     * @Route("-senza-dasc/{monthly_report}",      name="claims_hospital_senza_dasc_xls",    defaults={"monthly_report": false, "mode": "senza_dasc"},    options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-senza-gestore/{monthly_report}",   name="claims_hospital_senza_gestore_xls", defaults={"monthly_report": false, "mode": "senza_gestore"}, options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-recuperati/{monthly_report}",      name="claims_hospital_recuperati_xls",    defaults={"monthly_report": false, "mode": "recuperati"},    options={"ACL": {"in_role": {"C_RECUPERI_H"}}})
     * @Route("-recupero/{monthly_report}",        name="claims_hospital_recupero_xls",      defaults={"monthly_report": false, "mode": "recupero"},      options={"ACL": {"in_role": {"C_RECUPERI_H"}}})
     */
    public function xlsAction($mode, $monthly_report) {
        $excelService = $this->get('xls.service_xls5');
        /* @var $excelService \Liuggio\ExcelBundle\Service\ExcelContainer */
//        \Ephp\UtilityBundle\Utility\Debug::info($excelService->excelObj);

        $excel = $excelService->excelObj;
        /* @var $excel \PHPExcel */

        $excel->getProperties()->setCreator($this->getUser()->getCliente()->getNome())
                ->setLastModifiedBy($this->getUser()->getCliente()->getNome())
                ->setTitle(($monthly_report ? "Montly Report " : "Riepilogo " ) . date('m/Y'))
                ->setSubject(($monthly_report ? "Montly Report " : "Riepilogo " ) . date('m/Y'))
                ->setDescription("Generato automaticamente da JF-System")
                ->setKeywords($monthly_report ? "Montly Report, Hospital" : "Riepilogo, Hospital")
                ->setCategory($monthly_report ? "Montly Report Hospital" : "Riepilogo Hospital");

        $colonne = $this->getColonne($mode, $monthly_report);

        $filtri = $this->buildFiltri($mode);
        $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();

        $sheet = $excel->setActiveSheetIndex(0);
        /* @var $sheet \PHPExcel_Worksheet */

        $this->fillSheet($sheet, $colonne, $entities, $monthly_report);

        $excel->getActiveSheet()->setTitle($monthly_report ? "Montly Report Hospital" : "Riepilogo Hospital");
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        //create the response
        $response = $excelService->getResponse();
        /* @var $response \Symfony\Component\HttpFoundation\Response */
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . ($monthly_report ? "Monthly-report" . date('-m-Y') : "Riepilogo" . date('-d-m-Y') ) . '.xls;');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->sendHeaders();
        return $response;
    }

    /**
     * @Route("-stati",           name="claims_stati_hospital_all_xls",           defaults={"mode": "default", "stato": "default"},   options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stati-completo",  name="claims_stati_hospital_completo_all_xls",  defaults={"mode": "completo", "stato": "default"},  options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-stati-personale", name="claims_stati_hospital_personale_all_xls", defaults={"mode": "personale", "stato": "default"}, options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     */
    public function allXlsStatiAction($mode) {
        $excelService = $this->get('xls.service_xls5');
        /* @var $excelService \Liuggio\ExcelBundle\Service\ExcelContainer */
//        \Ephp\UtilityBundle\Utility\Debug::info($excelService->excelObj);

        $excel = $excelService->excelObj;
        /* @var $excel \PHPExcel */

        $excel->getProperties()->setCreator($this->getUser()->getCliente()->getNome())
                ->setLastModifiedBy($this->getUser()->getCliente()->getNome())
                ->setTitle("Stati pratica " . date('m/Y'))
                ->setSubject("Stati pratica " . date('m/Y'))
                ->setDescription("Generato automaticamente da JF-System")
                ->setKeywords("Stati pratica")
                ->setCategory("Stati pratica");

        $colonne = $this->getColonne($mode);

        $i = 0;
        foreach ($this->findBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $this->getUser()->getCliente()->getId(), 'tab' => true)) as $stato) {
            /* @var $stato \Claims\CoreBundle\Entity\StatoPratica */
            $_stato = $stato->getId();
            $filtri = $this->buildFiltri($mode, $_stato);
            $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();

            if ($i > 0) {
                $excel->createSheet($i);
            }
            $sheet = $excel->setActiveSheetIndex($i);

            $this->fillSheet($sheet, $colonne, $entities);

            $excel->getActiveSheet()->setTitle(\Ephp\UtilityBundle\Utility\String::tronca(str_replace(array('*', ':', '/', '\\', '?', '[', ']'), array(' ', ' ', '-', '-', '', '(', ')'), $stato->getStato()), 25));

            $i++;
        }
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        //create the response
        $response = $excelService->getResponse();
        /* @var $response \Symfony\Component\HttpFoundation\Response */
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . "Stati-pratica" . date('-d-m-Y') . '.xls;');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->sendHeaders();
        return $response;
    }

    /**
     * @Route("-stati/{stato}",           name="claims_stati_hospital_xls",           defaults={"mode": "default", "stato": "default"},   options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stati-completo/{stato}",  name="claims_stati_hospital_completo_xls",  defaults={"mode": "completo", "stato": "default"},  options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-stati-personale/{stato}", name="claims_stati_hospital_personale_xls", defaults={"mode": "personale", "stato": "default"}, options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     */
    public function xlsStatiAction($mode, $stato) {
        $excelService = $this->get('xls.service_xls5');
        /* @var $excelService \Liuggio\ExcelBundle\Service\ExcelContainer */
//        \Ephp\UtilityBundle\Utility\Debug::info($excelService->excelObj);

        $excel = $excelService->excelObj;
        /* @var $excel \PHPExcel */

        $excel->getProperties()->setCreator($this->getUser()->getCliente()->getNome())
                ->setLastModifiedBy($this->getUser()->getCliente()->getNome())
                ->setTitle("Stato pratica {$stato} " . date('m/Y'))
                ->setSubject("Stato pratica {$stato} " . date('m/Y'))
                ->setDescription("Generato automaticamente da JF-System")
                ->setKeywords("Stato pratica {$stato}")
                ->setCategory("Stato pratica {$stato}");

        $colonne = $this->getColonne($mode);

        $filtri = $this->buildFiltri($mode, $stato);
        $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();

        $sheet = $excel->setActiveSheetIndex(0);
        /* @var $sheet \PHPExcel_Worksheet */

        $this->fillSheet($sheet, $colonne, $entities);

        $stato = $this->findBy('ClaimsCoreBundle:StatoPratica', $stato);
        $excel->getActiveSheet()->setTitle(\Ephp\UtilityBundle\Utility\String::tronca(str_replace(array('*', ':', '/', '\\', '?', '[', ']'), array(' ', ' ', '-', '-', '', '(', ')'), $stato->getStato()), 25));

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);


        //create the response
        $response = $excelService->getResponse();
        /* @var $response \Symfony\Component\HttpFoundation\Response */
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . "Stato-pratica-" . \Doctrine\Common\Util\Inflector::camelize($stato->getStato()) . date('-d-m-Y') . '.xls;');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->sendHeaders();
        return $response;
    }

    /**
     * @Route("-slc",                 name="slc_hospital_xls",            defaults={"mode": "default"},    options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-slc/analisi-np",      name="slc_hospital_np_xls",         defaults={"mode": "np"},         options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-slc/analisi-np-sg",   name="slc_hospital_np_sg_xls",      defaults={"mode": "npsg"},       options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-slc/analisi-np-cg",   name="slc_hospital_np_cg_xls",      defaults={"mode": "npcg"},       options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-slc/analisi-riserve", name="slc_hospital_riserve_xls",    defaults={"mode": "riserve"},    options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-slc/bookeeping",      name="slc_hospital_bookeeping_xls", defaults={"mode": "bookeeping"}, options={"ACL": {"in_role": {"R_SUPER"}}})
     */
    public function xlsAnalisiAction($mode) {
        $excelService = $this->get('xls.service_xls5');
        /* @var $excelService \Liuggio\ExcelBundle\Service\ExcelContainer */
//        \Ephp\UtilityBundle\Utility\Debug::info($excelService->excelObj);

        $excel = $excelService->excelObj;
        /* @var $excel \PHPExcel */

        $excel->getProperties()->setCreator($this->getUser()->getCliente()->getNome())
                ->setLastModifiedBy($this->getUser()->getCliente()->getNome())
                ->setTitle("Analisi {$mode} " . date('m/Y'))
                ->setSubject("Analisi {$mode} " . date('m/Y'))
                ->setDescription("Generato automaticamente da JF-System")
                ->setKeywords("Analisi {$mode}")
                ->setCategory("Analisi {$mode}");

        $colonne = $this->getColonne($mode);

        $filtri = $this->buildFiltriSlc($mode);
        $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();

        $sheet = $excel->setActiveSheetIndex(0);
        /* @var $sheet \PHPExcel_Worksheet */

        $this->fillSheet($sheet, $colonne, $entities);

        $excel->getActiveSheet()->setTitle($mode);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        //create the response
        $response = $excelService->getResponse();
        /* @var $response \Symfony\Component\HttpFoundation\Response */
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . "Analisi-{$mode}-" . date('-d-m-Y') . '.xls;');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->sendHeaders();
        return $response;
    }

    protected function fillSheet(\PHPExcel_Worksheet $sheet, $colonne, $entities, $monthly_report = false) {
        foreach ($colonne as $colonna => $titolo) {
            $cell = $sheet->setCellValue($colonna . '1', $titolo['nome']);
            /* @var $cell \PHPExcel_Cell */
            $sheet->getStyle($colonna . '1')->applyFromArray(array('font' => array('bold' => true)))->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
            ;
        }
        $riga = 2;
        foreach ($entities as $entity) {
            /* @var $entity \Claims\HBundle\Entity\Pratica */
            foreach ($colonne as $colonna => $titolo) {
                $valore = '';
                switch ($titolo['nome']) {
                    case 'Pratica':
                        $valore = $entity->getCodice();
                        break;
                    case 'Dasc':
                        $valore = $entity->getDasc() ? $entity->getDasc()->format('d-m-Y') : '';
                        break;
                    case 'Giudiziale':
                        $valore = $entity->getCourt();
                        break;
                    case 'Claimant':
                        $valore = $entity->getClaimant();
                        break;
                    case 'Gestore':
                        $valore = $entity->getGestore() ? $entity->getGestore()->getSigla() : '-';
                        break;
                    case 'SOI':
                        $valore = $entity->getSoi();
                        break;
                    case 'Amount Reserved':
                        $valore = $entity->getAmountReserved() > 0 ? $entity->getAmountReserved() : 'N.P.';
                        break;
                    case 'First Reserve Indication':
                        $valore = $entity->getFirstReserveIndication();
                        break;
                    case 'Offerta Nostra':
                        $valore = $entity->getOffertaNostra();
                        break;
                    case 'Offerta Loro':
                        $valore = $entity->getOffertaLoro();
                        break;
                    case 'Recupero Offerta Nostra':
                        $valore = $entity->getRecuperoOffertaNostra();
                        if($valore <= 100) {
                            $valore = $valore/100;
                        }
                        break;
                    case 'Recupero Offerta Loro':
                        $valore = $entity->getRecuperoOffertaLoro();
                        if($valore <= 100) {
                            $valore = $valore/100;
                        }
                        break;
                    case 'LT Fees Paid':
                        $valore = $entity->getLtFeesPaid();
                        break;
                    case 'LT Fees Reserve':
                        $valore = $entity->getLtFeesReserve();
                        break;
                    case 'Dati recupero':
                        $valore = $entity->getDatiRecupero();
                        break;
                    case 'Note':
                        if ($monthly_report) {
                            if ($entity->getNote()) {
                                $txt = String::strip_tags($entity->getNote());
                                $valore = "Note
{$txt}".($entity->getNoteDataModifica() ? "
({$entity->getNoteDataModifica()->format('d-m-Y')})" : "");
                            } else {
                                $mr = $entity->getMonthlyReport();
                                if ($mr) {
                                    $txt = String::strip_tags($mr->getNote());
                                    $valore = "{$mr->getTitolo()}
{$txt}
({$mr->getDataOra()->format('d-m-Y')})";
                                }
                            }
                        } else {
                            $valore = $entity->getNote() ? $entity->getNote() : '';
                        }
                        break;
                    case 'Stato pratica':
                        $valore = $entity->getStatoPratica() ? $entity->getStatoPratica()->getStato() : '-';
                        break;
                }
                $cell = $sheet->setCellValue($colonna . $riga, $valore, true);
                /* @var $cell \PHPExcel_Cell */
                switch ($titolo['nome']) {
                    case 'SOI':
                    case 'Pratica':
                    case 'Dasc':
                    case 'Gestore':
                    case 'Giudiziale':
                        $sheet->getStyle($colonna . $riga)->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
                        break;
                    case 'Claimant':
                    case 'Stato pratica':
                        $sheet->getStyle($colonna . $riga)->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
                        break;
                    case 'Amount Reserved':
                    case 'First Reserve Indication':
                    case 'LT Fees Paid':
                    case 'LT Fees Reserve':
                    case 'Offerta Nostra':
                    case 'Offerta Loro':
                        $sheet->getStyle($colonna . $riga)->getNumberFormat()->setFormatCode('#,##0.00_-[$ €]');
                        $sheet->getStyle($colonna . $riga)->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
                        break;
                    case 'Recupero Offerta Nostra':
                    case 'Recupero Offerta Loro':
                        if($valore) {
                            if($valore <= 1) {
                                $sheet->getStyle($colonna . $riga)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                            } else {
                                $sheet->getStyle($colonna . $riga)->getNumberFormat()->setFormatCode('#,##0.00_-[$ €]');
                            }
                        }
                        $sheet->getStyle($colonna . $riga)->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
                        break;
                    case 'Dati recupero':
                    case 'Note':
                        $sheet->getStyle($colonna . $riga)->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => true));
                        break;
                }
            }
            $riga++;
        }
        foreach ($colonne as $colonna => $titolo) {
            if (isset($titolo['larghezza'])) {
                $sheet->getColumnDimension($colonna)->setWidth($titolo['larghezza']);
            } else {
                $sheet->getColumnDimension($colonna)->setWidth(10);
            }
        }
        foreach ($colonne as $colonna => $titolo) {
            $calculatedWidth = $sheet->getColumnDimension($colonna)->getWidth();
            $sheet->getColumnDimension($colonna)->setWidth((int) $calculatedWidth * 1.05);
        }
        $sheet->freezePaneByColumnAndRow(0, 1);
    }

    protected function getColonne($mode, $monthly_report = false) {
        $colonna = ord('A');
        $colonne = array();
        $colonne[chr($colonna++)] = array('nome' => 'Pratica', 'larghezza' => 20);
        $colonne[chr($colonna++)] = array('nome' => 'Dasc', 'larghezza' => 10);
        $colonne[chr($colonna++)] = array('nome' => 'Giudiziale', 'larghezza' => 10);
        $colonne[chr($colonna++)] = array('nome' => 'Claimant', 'larghezza' => 30);
        if (in_array($mode, array('aperti', 'completo', 'chiusi', 'senza_dasc', 'senza_gestore', 'np', 'npcg'))) {
            $colonne[chr($colonna++)] = array('nome' => 'Gestore', 'larghezza' => 10);
        }
        $colonne[chr($colonna++)] = array('nome' => 'SOI', 'larghezza' => 10);
        if (in_array($mode, array('np', 'bookeeping'))) {
            $colonne[chr($colonna++)] = array('nome' => 'LT Fees Paid', 'larghezza' => 20);
            $colonne[chr($colonna++)] = array('nome' => 'LT Fees Reserve', 'larghezza' => 20);
        } else {
            if (in_array($mode, array('recupero', 'recuperati'))) {
                $colonne[chr($colonna++)] = array('nome' => 'Offerta Nostra', 'larghezza' => 20);
                $colonne[chr($colonna++)] = array('nome' => 'Recupero Offerta Nostra', 'larghezza' => 20);
                $colonne[chr($colonna++)] = array('nome' => 'Offerta Loro', 'larghezza' => 20);
                $colonne[chr($colonna++)] = array('nome' => 'Recupero Offerta Loro', 'larghezza' => 20);
            } else {
                $colonne[chr($colonna++)] = array('nome' => 'Amount Reserved', 'larghezza' => 20);
                if (in_array($mode, array('np', 'reserve'))) {
                    $colonne[chr($colonna++)] = array('nome' => 'First Reserve Indication', 'larghezza' => 20);
                }
            }
        }
        if (!$monthly_report && $this->hasRole('C_RECUPERI_H')) {
            $colonne[chr($colonna++)] = array('nome' => 'Dati recupero', 'larghezza' => 50);
        } else {
            $colonne[chr($colonna++)] = array('nome' => 'Note', 'larghezza' => 50);
        }
        $colonne[chr($colonna++)] = array('nome' => 'Stato pratica', 'larghezza' => 20);
        return $colonne;
    }

}
