<?php

namespace SLC\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;

/**
 * @Route("/slc/claims-hospital")
 */
class TabelloneController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController,
        \Claims\HBundle\Controller\Traits\TabelloneController;

    /**
     * @Route("-xls/{monthly_report}",                 name="claims_hospital_xls",               defaults={"monthly_report": false, "mode": "default"},       options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-xls-personale/{monthly_report}",       name="claims_hospital_personale_xls",     defaults={"monthly_report": false, "mode": "personale"},     options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-xls-chiusi/{monthly_report}",          name="claims_hospital_chiuso_xls",        defaults={"monthly_report": false, "mode": "chiuso"},        options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-xls-tutti/{monthly_report}",           name="claims_hospital_tutti_xls",         defaults={"monthly_report": false, "mode": "tutti"},         options={"ACL": {"in_role": {"C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-xls-aperti/{monthly_report}",          name="claims_hospital_aperti_xls",        defaults={"monthly_report": false, "mode": "aperti"},        options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-xls-chiusi-completo/{monthly_report}", name="claims_hospital_chiusi_xls",        defaults={"monthly_report": false, "mode": "chiusi"},        options={"ACL": {"in_role": {"C_ADMIN", "C_RECUPERI_H"}}})
     * @Route("-xls-completo/{monthly_report}",        name="claims_hospital_completo_xls",      defaults={"monthly_report": false, "mode": "completo"},      options={"ACL": {"in_role": {"C_ADMIN", "C_RECUPERI_H"}}})
     * @Route("-xls-senza-dasc/{monthly_report}",      name="claims_hospital_senza_dasc_xls",    defaults={"monthly_report": false, "mode": "senza_dasc"},    options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Route("-xls-senza-gestore/{monthly_report}",   name="claims_hospital_senza_gestore_xls", defaults={"monthly_report": false, "mode": "senza_gestore"}, options={"ACL": {"in_role": {"C_ADMIN"}}})
     */
    public function xlsAction($mode, $monthly_report) {
        $filtri = $this->buildFiltri($mode);
        $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();

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

        $colonna = ord('A');
        $colonne = array();
        $colonne[chr($colonna++)] = array('nome' => 'Pratica', 'larghezza' => 20);
        $colonne[chr($colonna++)] = array('nome' => 'Dasc', 'larghezza' => 10);
        $colonne[chr($colonna++)] = array('nome' => 'Giudiziale', 'larghezza' => 10);
        $colonne[chr($colonna++)] = array('nome' => 'Claimant', 'larghezza' => 30);
        if (in_array($mode, array('aperti', 'completo', 'chiusi', 'senza_dasc', 'senza_gestore'))) {
            $colonne[chr($colonna++)] = array('nome' => 'Gestore', 'larghezza' => 10);
        }
        $colonne[chr($colonna++)] = array('nome' => 'SOI', 'larghezza' => 10);
        $colonne[chr($colonna++)] = array('nome' => 'Amount Reserved', 'larghezza' => 20);
        if (!$monthly_report && $this->hasRole('C_RECUPERI_H')) {
            $colonne[chr($colonna++)] = array('nome' => 'Dati recupero', 'larghezza' => 50);
        } else {
            $colonne[chr($colonna++)] = array('nome' => 'Note', 'larghezza' => 50);
        }
        $colonne[chr($colonna++)] = array('nome' => 'Stato pratica', 'larghezza' => 20);

        $sheet = $excel->setActiveSheetIndex(0);
        /* @var $sheet \PHPExcel_Worksheet */
        foreach ($colonne as $colonna => $titolo) {
            $cell = $sheet->setCellValue($colonna . '1', $titolo['nome']);
            /* @var $cell \PHPExcel_Cell */
            $sheet->getStyle($colonna . '1')->applyFromArray(array('font' => array('bold' => true)))->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));;
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
                    case 'Dati recupero':
                        $valore = $entity->getDatiRecupero();
                        break;
                    case 'Note':
                        if ($monthly_report) {
                            if ($entity->getNote()) {
                                $valore = "Note
{$entity->getNote()}";
                            } else {
                                $mr = $entity->getMonthlyReport();
                                if ($mr) {
                                    $valore = "{$mr->getTitolo()}
{$mr->getNote()}
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
//                        \Ephp\UtilityBundle\Utility\Debug::vd($sheet->getStyle($colonna . $riga)->getNumberFormat()); //->applyFromArray(array('font' => array('bold' => true)))->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
                        $sheet->getStyle($colonna . $riga)->getNumberFormat()->setFormatCode('#,##0.00_-[$ €]');
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

        $excel->getActiveSheet()->setTitle($monthly_report ? "Montly Report Hospital" : "Riepilogo Hospital");

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        //create the response
        $response = $excelService->getResponse();
        /* @var $response \Symfony\Component\HttpFoundation\Response */
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . ($monthly_report ? "Monthly-report" : "Riepilogo-" ) . date('-m-Y') . '.xls;');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->sendHeaders();
        return $response;
    }


}
