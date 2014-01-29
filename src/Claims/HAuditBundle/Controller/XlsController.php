<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;
use Ephp\UtilityBundle\Utility\String;
use Claims\HAuditBundle\Entity\Audit;

/**
 * @Route("/claims-h-audit-xls")
 */
class XlsController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController;

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("/{id}", name="claims-h-audit-xls")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     */
    public function downloadAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        $excelService = $this->get('xls.service_xls5');
        /* @var $excelService \Liuggio\ExcelBundle\Service\ExcelContainer */

        $excel = $excelService->excelObj;
        /* @var $excel \PHPExcel */

        $excel->getProperties()->setCreator($this->getUser()->getCliente()->getNome())
                ->setLastModifiedBy($this->getUser()->getCliente()->getNome())
                ->setTitle('Hospital Audit ' . $entity->getLuogo() . ' ' . date('m/Y'))
                ->setSubject('Hospital Audit ' . $entity->getLuogo() . ' ' . date('m/Y'))
                ->setDescription("Generato automaticamente da JF-System")
                ->setKeywords('Hospital Audit ' . $entity->getLuogo() . ", Hospital")
                ->setCategory('Hospital Audit ' . $entity->getLuogo() . " Hospital");

        $colonne = $this->getColonne($entity);

        $sheet = $excel->setActiveSheetIndex(0);
        /* @var $sheet \PHPExcel_Worksheet */
        
        $excel->getActiveSheet()->setTitle('Hospital Audit');

        $this->fillSheet($sheet, $colonne, $entity->getPratiche());

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        //create the response
        $response = $excelService->getResponse();
        /* @var $response \Symfony\Component\HttpFoundation\Response */
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . (str_replace(' ', '_', 'Hospital Audit ' . $entity->getLuogo()) . date('-d-m-Y') ) . '.xls;');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->sendHeaders();
        return $response;
    }

    protected function fillSheet(\PHPExcel_Worksheet $sheet, $colonne, $entities) {
        foreach ($colonne as $colonna => $titolo) {
            $cell = $sheet->setCellValue($colonna . '1', $titolo['nome']);
            /* @var $cell \PHPExcel_Cell */
            $sheet->getStyle($colonna . '1')->applyFromArray(array('font' => array('bold' => true)))->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
        }
        $riga = 2;
        foreach ($entities as $entity) {
            /* @var $entity \Claims\HAuditBundle\Entity\Pratica */
            foreach ($colonne as $colonna => $titolo) {
                $valore = '';
                switch ($titolo['nome']) {
                    case 'Number':
                        $valore = $entity->getCodice();
                        break;
                    case 'Claimant':
                        $valore = $entity->getClaimant();
                        break;
                    case 'Group':
                        $valore = $entity->getGruppo();
                        break;
                    //TODO DA QUI
                    default:
                        $q = $entity->getValue($titolo['id']);
                        if ($q) {
                            if ($q->getQuestion()->getType() == 'checkbox') {
                                $valore = implode(', ', $q->getResponses());
                            } else {
                                $valore = $q->getResponse();
                            }
                        } else {
                            $valore = '';
                        }
                        break;
                }
                $cell = $sheet->setCellValue($colonna . $riga, $valore, true);
                /* @var $cell \PHPExcel_Cell */
                switch ($titolo['nome']) {
                    case 'Claimant':
                    case 'Group':
                    case 'Number':
                        $sheet->getStyle($colonna . $riga)->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
                        break;
                    default:
                        $q = $entity->getValue($titolo['id']);
                        if ($q) {
                            switch ($q->getQuestion()->getType()) {
                                case 'money':
                                    $sheet->getStyle($colonna . $riga)->getNumberFormat()->setFormatCode('#,##0.00_-[$ €]');
                                    $sheet->getStyle($colonna . $riga)->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
                                    break;
                                case 'text':
                                case 'textarea':
                                case 'checkbox':
                                    $sheet->getStyle($colonna . $riga)->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => true));
                                    break;
                                case 'select':
                                    $sheet->getDataValidation($colonna . $riga)->setShowDropDown()
                                            ->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
                                            ->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
                                            ->setAllowBlank(true)
                                            ->setShowInputMessage(true)
                                            ->setShowErrorMessage(true)
                                            ->setShowDropDown(true)
                                            ->setErrorTitle('Input error')
                                            ->setError('Value is not in list.')
                                            ->setPromptTitle('Pick from list')
                                            ->setPrompt('Please pick a value from the drop-down list.')
                                            ->setFormula1('\'Hospital Audit\'!$C$1:$C$10');
                                    $sheet->getStyle($colonna . $riga)->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
                                    break;

                                default:
                                    $sheet->getStyle($colonna . $riga)->getAlignment()->applyFromArray(array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => false));
                                    break;
                            }
                        }
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

    protected function getColonne(\Claims\HAuditBundle\Entity\Audit $audit) {
        $prefix = '';
        $nextprefix = ord('A');
        $colonna = ord('A');
        $colonne = array();
        $colonne[$prefix . chr($colonna++)] = array('nome' => 'Group', 'larghezza' => 30);
        $colonne[$prefix . chr($colonna++)] = array('nome' => 'Number', 'larghezza' => 20);
        $colonne[$prefix . chr($colonna++)] = array('nome' => 'Claimant', 'larghezza' => 30);
        foreach ($audit->getQuestion() as $question) {
            /* @var $question \Claims\HAuditBundle\Entity\AuditQuestion */
            $q = $question->getQuestion();
            switch ($q->getType()) {
                case 'textarea':
                    $colonne[$prefix . chr($colonna++)] = array('nome' => $q->getQuestion(), 'larghezza' => 50, 'id' => $q->getId());
                    break;
                case 'number':
                case 'date':
                    $colonne[$prefix . chr($colonna++)] = array('nome' => $q->getQuestion(), 'larghezza' => 10, 'id' => $q->getId());
                    break;
                case 'money':
                case 'select':
                case 'checkbox':
                    $colonne[$prefix . chr($colonna++)] = array('nome' => $q->getQuestion(), 'larghezza' => 20, 'id' => $q->getId());
                    break;
            }
            if (ord('Z') < $colonna) {
                $prefix = chr($nextprefix);
                $nextprefix++;
                $colonna = ord('A');
            }
        }
        return $colonne;
    }

    /**
     * @Route("-form/{audit}", name="claims-h-audit-xls-file")
     * @Template()
     */
    public function formAction($audit) {
        $entity = $this->find('ClaimsHAuditBundle:Audit', $audit);
        return array('audit' => $audit, 'entity' => $entity);
    }

    /**
     * @Route("-callback", name="claims-h-audit-xls-file-callback", options={"expose": true})
     * @Template()
     */
    public function callbackAction() {
        set_time_limit(3600);
        $source = __DIR__ . '/../../../../web' . $this->getParam('file');
        $audit = $this->find('ClaimsHAuditBundle:Audit', $this->getParam('audit'));
        if ($audit) {
            $this->getRepository('ClaimsHAuditBundle:Pratica')->cancellaAudit($audit);
        }
        $out = $this->importBdx($this->getUser()->getCliente(), $source, $audit);
        $out['audit'] = $audit->getId();
        return $out;
    }

    private function importBdx(\JF\ACLBundle\Entity\Cliente $cliente, $source, \Claims\HAuditBundle\Entity\Audit $audit) {
        $data = new SpreadsheetExcelReader($source, true, 'UTF-8');
        $pratiche = array();
        //return new \Symfony\Component\HttpFoundation\Response(json_encode($data->sheets));
        foreach ($data->sheets as $sheet) {
            $sheet = $sheet['cells'];
            $start = false;
            $colonne = array();
            foreach ($sheet as $riga => $valori_riga) {
                if (!$start) {
                    if (isset($valori_riga[1]) && in_array($valori_riga[1], array('Group', 'GROUP', 'Gruppo', 'GRUPPO'))) {
                        $colonne = $valori_riga;
                        $start = true;
                    }
                } else {
                    if (!isset($valori_riga[1]) || !$valori_riga[1]) {
                        continue;
                    } else {
                        try {
                            $this->getEm()->beginTransaction();
                            $pratica = new Pratica();
                            $pratica->setAudit($audit);
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
                                    case 'TPA REF':
                                        $pratica->setTpa($value);
                                        $pratica->setCodice($value);
                                        break;

                                    case 'GROUP':
                                    case 'Group':
                                    case 'GRUPPO':
                                    case 'Gruppo':
                                        $pratica->setGruppo($value);
                                        break;

                                    case 'MONTH':
                                    case 'Month':
                                        $pratica->setMese($value);
                                        break;

                                    case 'MFREF':
                                        $pratica->setMfRef($value);
                                        break;

                                    case 'HOSPITAL':
                                        $pratica->setOspedale($value);
                                        break;

                                    case 'YOA':
                                        $pratica->setAnno($value);
                                        break;

                                    case 'DS CODE':
                                        $pratica->setDsCode($value);
                                        break;

                                    case 'CLAYMANT':
                                    case 'CLAIMANT':
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
                                    case 'Payments':
                                    case 'Payments ':
                                    case 'PAYMENTS':
                                    case 'PAYMENTS ':
                                        if ($value) {
                                            $pratica->setPayment(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'RESERVE':
                                        if ($value) {
                                            $pratica->setReserve(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'PRO RESERVE':
                                        if ($value) {
                                            $pratica->setProReserve(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    default: break;
                                }
                            }
                            $pratica->setDataImport(new \DateTime());
                            $pratica->addLog(array('Importata pratica'));
                            $this->persist($pratica);
                            $pratiche[] = $pratica;
                            $this->getEm()->commit();
                        } catch (\Exception $e) {
                            $this->getEm()->rollback();
                            throw $e;
                        }
                    }
                }
            }
        }

        return array('pratiche' => $pratiche);
    }

}