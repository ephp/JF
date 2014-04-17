<?php

namespace SLC\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;
use Ephp\UtilityBundle\Utility\String;
use Ephp\UtilityBundle\PhpWord\HtmlConverter;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Route("/slc/claims-hospital-word")
 */
class WordController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController,
        \Claims\HBundle\Controller\Traits\TabelloneController,
        Traits\TabelloneController;

    /**
     * @Route("-old-word-report/{slug}", name="claims_hospital_word_report_old", options={"expose": true, "ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     */
    public function oldWordReportAction($slug) {
        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica \Claims\HBundle\Entity\Pratica */

        if ($this->getUser()->getCliente()->getId() != $pratica->getCliente()->getId()) {
            return $this->createNotFoundException('Utente non autorizzato');
        }

        $twig = 'SLCHBundle:Tabellone:stampaTabellaReport.html.twig';

        if (!$pratica->getGestore()) {
            $pratica->setGestore($this->getUser());
        }

        $html = $this->render($twig, array('entity' => $pratica));

        $word = new HtmlConverter("Report " . $pratica);
        $out = $word->createDoc($html);

        $response = $this->render('EphpUtilityBundle::word.html.twig', $out);
        /* @var $response \Symfony\Component\HttpFoundation\Response */
        $response->headers->set('Content-Type', 'application/octet-stream; charset=utf-8;');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $out['filename'] . ';');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->sendHeaders();
        return $response;
    }

    /**
     * @Route("-word-report/{slug}", name="claims_hospital_word_report", options={"expose": true, "ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     */
    public function wordReportAction($slug) {
        $larghezza = 8500;

        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica \Claims\HBundle\Entity\Pratica */

        if ($this->getUser()->getCliente()->getId() != $pratica->getCliente()->getId()) {
            return $this->createNotFoundException('Utente non autorizzato');
        }

        if (!$pratica->getGestore()) {
            $pratica->setGestore($this->getUser());
        }

        $wordService = $this->get('phpword');
        /* @var $wordService \Ephp\OfficeBundle\FactoryWord */

        $PHPWord = $wordService->createPHPWordObject();
        /* @var $excel \PHPWord */
        
        // New portrait section
        $section = $PHPWord->createSection();

        $PHPWord->addFontStyle('rStyle', array('bold' => true, 'italic' => true, 'size' => 11));
        $PHPWord->addParagraphStyle('pStyle', array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0));
        $PHPWord->addParagraphStyle('tdStyle', array('align' => 'justify', 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0));

        $section->addText('Claims Reporting', 'rStyle', 'pStyle');
        $section->addText('Newline – Claims ' . $this->range($pratica->getAmountReserved()), 'rStyle', 'pStyle');

        $borderTable = array('width' => 100, 'borderSize' => 1, 'borderColor' => '000000');
        $styleFirstRow = array();
        $PHPWord->addTableStyle('Border Table', $borderTable, $styleFirstRow);
        
        $styleCell = array('width' => round($larghezza / 2), 'valign' => 'top');

        $fontBold = array('bold' => true);
        $fontNormal = array('bold' => false);

        $table = $section->addTable();
        /* @var $table \PhpOffice\PhpWord\Section\Table */

        // Riga 1
        $table->addRow();
        $claimant = $table->addCell(null, $styleCell);
        $claimant->addText('Claimant: ' . $pratica->getClaimant(), $fontBold, 'tdStyle');
        $claimant->addText(' ' . $pratica->getReportClaimant(), $fontNormal, 'tdStyle');
        $tpa = $table->addCell(null, $styleCell);
        $tpa->addText('TPA: ' . $pratica->getOspedale()->getSistema()->getNome(), $fontBold, 'tdStyle');

        // Riga 2
        $table->addRow();
        $soi = $table->addCell(null, $styleCell);
        $soi->addText('SOI:', $fontBold, 'tdStyle');
        $soi->addText($pratica->getReportSoi(true), $fontNormal, 'tdStyle');
        $nClaim = $table->addCell(null, $styleCell);
        $nClaim->addText('N.Claim: ' . $pratica->getCodice(), $fontBold, 'tdStyle');

        // Riga 3
        $table->addRow();
        $datiPolizza = $table->addCell(null, $styleCell);
        $datiPolizza->addText('Dati polizza:', $fontBold, 'tdStyle');
        $this->cellText($datiPolizza, $pratica->getReportVerificaCopertura(), $fontNormal, 'tdStyle');
        $table->addCell(null, $styleCell);

        // Riga 4
        $table->addRow();
        $dol = $table->addCell(null, $styleCell);
        $dol->addText('Data evento (DOL): ' . $pratica->getReportDol()->format('d/m/Y'), $fontBold, 'tdStyle');
        $don = $table->addCell(null, $styleCell);
        $don->addText('Data reclamo (DON): ' . $pratica->getReportDon()->format('d/m/Y'), $fontBold, 'tdStyle');

        // Riga 5
        $table->addRow();
        $descEvento = $table->addCell(null, $styleCell);
        $descEvento->addText('Descrizione Evento:', $fontBold, 'tdStyle');
        $this->cellText($descEvento, $pratica->getReportDescrizione(), $fontNormal, 'tdStyle');
        $table->addCell(null, $styleCell);

        // Riga 6
        $table->addRow();
        $mpl = $table->addCell(null, $styleCell);
        $mpl->addText('MPL:', $fontBold, 'tdStyle');
        $mpl->addText($pratica->getReportMpl(true), $fontNormal, 'tdStyle');
        $table->addCell(null, $styleCell);

        // Riga 7
        $table->addRow();
        $giudiziale = $table->addCell(null, $styleCell);
        if($pratica->getReportGiudiziale()) {
            $giudiziale->addText('Giudiziale: ', $fontBold, 'tdStyle');
            $this->cellText($giudiziale, $pratica->getReportGiudiziale(), $fontNormal, 'tdStyle');
        }
        $table->addCell(null, $styleCell);

        // Riga 8
        $table->addRow();
        $altrePolizze = $table->addCell(null, $styleCell);
        $altrePolizze->addText('Altre polizze: ', $fontBold, 'tdStyle');
        $this->cellText($altrePolizze, $pratica->getReportOtherPolicies(), $fontNormal, 'tdStyle');
        $table->addCell(null, $styleCell);

        // Riga 9
        $table->addRow();
        $tpl = $table->addCell(null, $styleCell);
        $tpl->addText('Tipo danno (TPL):', $fontBold, 'tdStyle');
        $tpl->addText($pratica->getReportTypeOfLoss(true), $fontNormal, 'tdStyle');
        $dipartimento = $table->addCell(null, $styleCell);
        $dipartimento->addText('Dipartimento ospedaliero:', $fontBold, 'tdStyle');
        $dipartimento->addText($pratica->getReportServiceProvider(true), $fontNormal, 'tdStyle');

        // Riga 10
        $table->addRow();
        $recupero = $table->addCell(null, $styleCell);
        $recupero->addText('Possibile recupero: ' . ($pratica->getReportPossibleRecovery() < 0 ? 'N.P.' : $pratica->getReportPossibleRecovery() . ' €'), $fontBold, 'tdStyle');
        $riserva = $table->addCell(null, $styleCell);
        $riserva->addText('Riserva: ' . ($pratica->getReportAmountReserved() < 0 ? 'N.P.' : $pratica->getReportAmountReserved() . ' €'), $fontBold, 'tdStyle');

        // Riga 11
        $table->addRow();
        $table->addCell(null, $styleCell);
        $franchigia = $table->addCell(null, $styleCell);
        $franchigia->addText('Franchigia Applicata:', $pratica->getReportApplicableDeductable() . ' €');

        // Riga 12
        $table->addRow();
        $azioni = $table->addCell(null, $styleCell);
        $azioni->addText('Azioni:', $fontBold, 'tdStyle');
        $this->cellText($azioni, $pratica->getReportFutureConduct(), $fontNormal, 'tdStyle');
        $table->addCell(null, $styleCell);

        // Riga 13
        $table->addRow();
        $table->addCell(null, $styleCell);
        $avv = $table->addCell(null, $styleCell);
        $avv->addText('Avv: ' . $pratica->getGestore()->getNome());

        foreach ($pratica->getReports() as $report) {
            $sectionReport = $PHPWord->createSection();
            /* @var $report \Claims\HBundle\Entity\Report */
//            $section->addPageBreak();

            $sectionReport->addText('Report ' . $report->getData()->format('d/m/Y'), 'rStyle', 'pStyle');
            $sectionReport->addTextBreak(1);

            $tableReport = $sectionReport->addTable();
            /* @var $tableReport \PhpOffice\PhpWord\Section\Table */

            // Riga 0
            $tableReport->addRow();
            $sx00 = $tableReport->addCell(null, $styleCell);
            $sx00->addText('TPA: ' . $pratica->getOspedale()->getSistema()->getNome(), $fontBold, 'tdStyle');
            $dx00 = $tableReport->addCell(null, $styleCell);
            $dx00->addText('Claimant: ' . $pratica->getClaimant(), $fontBold, 'tdStyle');
  
            // Riga 1
            $tableReport->addRow();
            $sx01 = $tableReport->addCell(null, $styleCell);
            $sx01->addText('Copertura:', $fontBold, 'tdStyle');
            $dx01 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx01, $report->getCopertura(), $fontNormal, 'tdStyle');

            // Riga 2
            $tableReport->addRow();
            $sx02 = $tableReport->addCell(null, $styleCell);
            $sx02->addText('Stato:', $fontBold, 'tdStyle');
            $dx02 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx02, $report->getStato(), $fontNormal, 'tdStyle');

            // Riga 3
            $tableReport->addRow();
            $sx03 = $tableReport->addCell(null, $styleCell);
            $sx03->addText('Descrizione in fatto:', $fontBold, 'tdStyle');
            $dx03 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx03, $report->getDescrizioneInFatto(), $fontNormal, 'tdStyle');

            // Riga 4
            $tableReport->addRow();
            $sx04 = $tableReport->addCell(null, $styleCell);
            $sx04->addText('Relazione avversaria:', $fontBold, 'tdStyle');
            $dx04 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx04, $report->getRelazioneAvversaria(), $fontNormal, 'tdStyle');

            // Riga 5
            $tableReport->addRow();
            $sx05 = $tableReport->addCell(null, $styleCell);
            $sx05->addText('Relazioni ex-adverso:', $fontBold, 'tdStyle');
            $dx05 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx05, $report->getRelazioneExAdverso(), $fontNormal, 'tdStyle');

            // Riga 6
            $tableReport->addRow();
            $sx06 = $tableReport->addCell(null, $styleCell);
            $sx06->addText('Medico Legale 1:', $fontBold, 'tdStyle');
            $dx06 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx06, $report->getMedicoLegale1(), $fontNormal, 'tdStyle');

            // Riga 7
            $tableReport->addRow();
            $sx07 = $tableReport->addCell(null, $styleCell);
            $sx07->addText('Medico Legale 2:', $fontBold, 'tdStyle');
            $dx07 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx07, $report->getMedicoLegale2(), $fontNormal, 'tdStyle');

            // Riga 8
            $tableReport->addRow();
            $sx08 = $tableReport->addCell(null, $styleCell);
            $sx08->addText('Medico Legale 3:', $fontBold, 'tdStyle');
            $dx08 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx08, $report->getMedicoLegale3(), $fontNormal, 'tdStyle');

            // Riga 9
            $tableReport->addRow();
            $sx09 = $tableReport->addCell(null, $styleCell);
            $sx09->addText('Valutazione responsabilità:', $fontBold, 'tdStyle');
            $dx09 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx09, $report->getValutazioneResponsabilita(), $fontNormal, 'tdStyle');

            // Riga 10
            $tableReport->addRow();
            $sx10 = $tableReport->addCell(null, $styleCell);
            $sx10->addText('Analisi danno (MPL):', $fontBold, 'tdStyle');
            $dx10 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx10, $report->getAnalisiDanno(), $fontNormal, 'tdStyle');

            // Riga 11
            $tableReport->addRow();
            $sx11 = $tableReport->addCell(null, $styleCell);
            $sx11->addText('Riserva:', $fontBold, 'tdStyle');
            $dx11 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx11, $report->getRiserva(), $fontNormal, 'tdStyle');

            // Riga 12
            $tableReport->addRow();
            $sx12 = $tableReport->addCell(null, $styleCell);
            $sx12->addText('Possibile recupero:', $fontBold, 'tdStyle');
            $dx12 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx12, $report->getPossibileRivalsa(), $fontNormal, 'tdStyle');

            // Riga 13
            $tableReport->addRow();
            $sx13 = $tableReport->addCell(null, $styleCell);
            $sx13->addText('Azioni:', $fontBold, 'tdStyle');
            $dx13 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx13, $report->getAzioni(), $fontNormal, 'tdStyle');

            // Riga 14
            $tableReport->addRow();
            $sx14 = $tableReport->addCell(null, $styleCell);
            $sx14->addText('Richiesta SA:', $fontBold, 'tdStyle');
            $dx14 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx14, $report->getRichiestaSa(), $fontNormal, 'tdStyle');

            // Riga 15
            $tableReport->addRow();
            $sx15 = $tableReport->addCell(null, $styleCell);
            $sx15->addText('Note:', $fontBold, 'tdStyle');
            $dx15 = $tableReport->addCell(null, $styleCell);
            $this->cellText($dx15, $report->getNote(), $fontNormal, 'tdStyle');
/*
*/
        }

        // create the writer
        $writer = $wordService->createWriter($PHPWord, 'Word2007');
//        $writer = $wordService->createWriter($PHPWord, 'RTF');
        // create the response
        $response = $wordService->createStreamedResponse($writer);

        /* @var $response \Symfony\Component\HttpFoundation\Response */
        $response->headers->set('Content-Type', 'application/rtfn/vnd.openxmlformats-officedocument.wordprocessingml.document; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=report_' . \Doctrine\Common\Util\Inflector::camelize($pratica->getClaimant()) . date('_d-m-Y') . '.docx;');
//        $response->headers->set('Content-Type', 'application/rtf; charset=utf-8');
//        $response->headers->set('Content-Disposition', 'attachment; filename=report_' . \Doctrine\Common\Util\Inflector::camelize($pratica->getClaimant()) . date('_d-m-Y') . '.rtf;');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->sendHeaders();
        return $response;
    }

    private function cellText(\PhpOffice\PhpWord\Section\Table\Cell $cell, $text, $style1, $style2) {
        $texts = explode("\n", $text);
        if(count($texts) == 0 || count($texts) == 1 && trim($texts[0]) == '') {
            $cell->addText('-', $style1, $style2);
        }
        foreach($texts as $t) {
            $cell->addText($t, $style1, $style2);
        }
    }

    private function range($ar) {
        return "over € 100,000.00";
        if ($ar < 0) {
            return "N.P.";
        }
        if ($ar < 50000) {
            return "under € 50.000";
        }
        if ($ar >= 50000) {
            return "over € 50.000";
        }
    }

}
