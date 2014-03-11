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
        $larghezza = 10;

        $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('slug' => $slug));
        /* @var $pratica \Claims\HBundle\Entity\Pratica */

        if ($this->getUser()->getCliente()->getId() != $pratica->getCliente()->getId()) {
            return $this->createNotFoundException('Utente non autorizzato');
        }

        if (!$pratica->getGestore()) {
            $pratica->setGestore($this->getUser());
        }

        // New Word Document
        $PHPWord = new \PHPWord();

        // New portrait section
        $section = $PHPWord->createSection();

        $PHPWord->addFontStyle('rStyle', array('bold' => true, 'italic' => true, 'size' => 11));
        $PHPWord->addParagraphStyle('pStyle', array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0));
        $PHPWord->addParagraphStyle('tdStyle', array('align' => 'justify', 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0));

        $section->addText('Claims Reporting', 'rStyle', 'pStyle');
        $section->addText('Newline – Claims ' . $this->range($pratica->getAmountReserved()), 'rStyle', 'pStyle');

        $styleTable = array('borderSize' => 5, 'borderColor' => '006699', 'cellMargin' => 5, 'bgColor' => 'efefef');
        $styleCell = array('valign' => 'justify');

        $fontBold = array('bold' => true);
        $fontNormal = array('bold' => false);

        $table = $section->addTable();
        /* @var $table \PHPWord_Section_Table */

        // Riga 1
        $table->addRow();
        $claimant = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $claimant->addText('Claimant: ' . $pratica->getClaimant(), $fontBold, 'tdStyle');
        $claimant->addText(' ' . $pratica->getReportClaimant(), $fontNormal, 'tdStyle');
        $tpa = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $tpa->addText('TPA: ' . $pratica->getOspedale()->getSistema()->getNome(), $fontBold, 'tdStyle');

        // Riga 2
        $table->addRow();
        $soi = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $soi->addText('SOI:', $fontBold, 'tdStyle');
        $soi->addText($pratica->getReportSoi(true), $fontNormal, 'tdStyle');
        $nClaim = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $nClaim->addText('N.Claim: ' . $pratica->getCodice(), $fontBold, 'tdStyle');

        // Riga 3
        $table->addRow();
        $datiPolizza = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $datiPolizza->addText('Dati polizza:', $fontBold, 'tdStyle');
        $datiPolizza->addText($pratica->getReportVerificaCopertura(), $fontNormal, 'tdStyle');
        $table->addCell($larghezza / 2, $styleCell, $styleTable);

        // Riga 4
        $table->addRow();
        $dol = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $dol->addText('Data evento (DOL): ' . $pratica->getReportDol()->format('d/m/Y'), $fontBold, 'tdStyle');
        $don = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $don->addText('Data reclamo (DON): ' . $pratica->getReportDon()->format('d/m/Y'), $fontBold, 'tdStyle');

        // Riga 5
        $table->addRow();
        $descEvento = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $descEvento->addText('Descrizione Evento:', $fontBold, 'tdStyle');
        $descEvento->addText($pratica->getReportVerificaCopertura(), $fontNormal, 'tdStyle');
        $table->addCell($larghezza / 2, $styleCell, $styleTable);

        // Riga 6
        $table->addRow();
        $mpl = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $mpl->addText('MPL:', $fontBold, 'tdStyle');
        $mpl->addText($pratica->getReportMpl(true), $fontNormal, 'tdStyle');
        $table->addCell($larghezza / 2, $styleCell, $styleTable);

        // Riga 7
        $table->addRow();
        $giudiziale = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        if($pratica->getReportGiudiziale()) {
            $giudiziale->addText('Giudiziale: ', $fontBold, 'tdStyle');
            $giudiziale->addText($pratica->getReportGiudiziale(), $fontNormal, 'tdStyle');
        }
        $table->addCell($larghezza / 2, $styleCell, $styleTable);

        // Riga 8
        $table->addRow();
        $altrePolizze = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $altrePolizze->addText('Altre polizze: ', $fontBold, 'tdStyle');
        $altrePolizze->addText($pratica->getReportOtherPolicies(), $fontNormal, 'tdStyle');
        $table->addCell($larghezza / 2, $styleCell, $styleTable);

        // Riga 9
        $table->addRow();
        $tpl = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $tpl->addText('Tipo danno (TPL):', $fontBold, 'tdStyle');
        $tpl->addText($pratica->getReportTypeOfLoss(true), $fontNormal, 'tdStyle');
        $dipartimento = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $dipartimento->addText('Dipartimento ospedaliero:', $fontBold, 'tdStyle');
        $dipartimento->addText($pratica->getReportServiceProvider(true), $fontNormal, 'tdStyle');

        // Riga 10
        $table->addRow();
        $recupero = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $recupero->addText('Possibile recupero: ' . ($pratica->getReportPossibleRecovery() < 0 ? 'N.P.' : $pratica->getReportPossibleRecovery() . ' €'), $fontBold, 'tdStyle');
        $riserva = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $riserva->addText('Riserva: ' . ($pratica->getReportAmountReserved() < 0 ? 'N.P.' : $pratica->getReportAmountReserved() . ' €'), $fontBold, 'tdStyle');

        // Riga 11
        $table->addRow();
        $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $franchigia = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $franchigia->addText('Franchigia Applicata:', $pratica->getReportApplicableDeductable() . ' €');

        // Riga 12
        $table->addRow();
        $azioni = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $azioni->addText('Azioni:', $fontBold, 'tdStyle');
        $azioni->addText($pratica->getReportFutureConduct(), $fontNormal, 'tdStyle');
        $table->addCell($larghezza / 2, $styleCell, $styleTable);

        // Riga 13
        $table->addRow();
        $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $avv = $table->addCell($larghezza / 2, $styleCell, $styleTable);
        $avv->addText('Avv: ' . $pratica->getGestore()->getNome());

        foreach ($pratica->getReports() as $report) {
            /* @var $report \Claims\HBundle\Entity\Report */
            $section->addPageBreak();

            $section->addText('Report ' . $report->getData()->format('d/m/Y'), 'rStyle', 'pStyle');
            $section->addTextBreak(1);

            $tableReport = $section->addTable();
            /* @var $tableReport \PHPWord_Section_Table */

            // Riga 0
            $tableReport->addRow();
            $sx00 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx00->addText('TPA: ' . $pratica->getOspedale()->getSistema()->getNome(), $fontBold, 'tdStyle');
            $dx00 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx00->addText('Claimant: ' . $pratica->getClaimant(), $fontBold, 'tdStyle');

            // Riga 1
            $tableReport->addRow();
            $sx01 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx01->addText('Copertura:', $fontBold, 'tdStyle');
            $dx01 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx01->addText($report->getCopertura(), $fontNormal, 'tdStyle');

            // Riga 2
            $tableReport->addRow();
            $sx02 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx02->addText('Stato:', $fontBold, 'tdStyle');
            $dx02 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx02->addText($report->getStato(), $fontNormal, 'tdStyle');

            // Riga 3
            $tableReport->addRow();
            $sx03 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx03->addText('Descrizione in fatto:', $fontBold, 'tdStyle');
            $dx03 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx03->addText($report->getDescrizioneInFatto(), $fontNormal, 'tdStyle');

            // Riga 4
            $tableReport->addRow();
            $sx04 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx04->addText('Relazione avversaria:', $fontBold, 'tdStyle');
            $dx04 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx04->addText($report->getRelazioneAvversaria(), $fontNormal, 'tdStyle');

            // Riga 5
            $tableReport->addRow();
            $sx05 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx05->addText('Relazioni ex-adverso:', $fontBold, 'tdStyle');
            $dx05 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx05->addText($report->getRelazioneExAdverso(), $fontNormal, 'tdStyle');

            // Riga 6
            $tableReport->addRow();
            $sx06 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx06->addText('Medico Legale 1:', $fontBold, 'tdStyle');
            $dx06 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx06->addText($report->getMedicoLegale1(), $fontNormal, 'tdStyle');

            // Riga 7
            $tableReport->addRow();
            $sx07 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx07->addText('Medico Legale 2:', $fontBold, 'tdStyle');
            $dx07 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx07->addText($report->getMedicoLegale2(), $fontNormal, 'tdStyle');

            // Riga 8
            $tableReport->addRow();
            $sx08 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx08->addText('Medico Legale 3:', $fontBold, 'tdStyle');
            $dx08 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx08->addText($report->getMedicoLegale3(), $fontNormal, 'tdStyle');

            // Riga 9
            $tableReport->addRow();
            $sx09 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx09->addText('Valutazione responsabilità:', $fontBold, 'tdStyle');
            $dx09 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx09->addText($report->getValutazioneResponsabilita(), $fontNormal, 'tdStyle');

            // Riga 10
            $tableReport->addRow();
            $sx10 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx10->addText('Analisi danno (MPL):', $fontBold, 'tdStyle');
            $dx10 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx10->addText($report->getAnalisiDanno(), $fontNormal, 'tdStyle');

            // Riga 11
            $tableReport->addRow();
            $sx11 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx11->addText('Riserva:', $fontBold, 'tdStyle');
            $dx11 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx11->addText($report->getRiserva(), $fontNormal, 'tdStyle');

            // Riga 12
            $tableReport->addRow();
            $sx12 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx12->addText('Possibile recupero:', $fontBold, 'tdStyle');
            $dx12 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx12->addText($report->getPossibileRivalsa(), $fontNormal, 'tdStyle');

            // Riga 13
            $tableReport->addRow();
            $sx13 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx13->addText('Azioni:', $fontBold, 'tdStyle');
            $dx13 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx13->addText($report->getAzioni(), $fontNormal, 'tdStyle');

            // Riga 14
            $tableReport->addRow();
            $sx14 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx14->addText('Richiesta SA:', $fontBold, 'tdStyle');
            $dx14 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx14->addText($report->getRichiestaSa(), $fontNormal, 'tdStyle');

            // Riga 15
            $tableReport->addRow();
            $sx15 = $tableReport->addCell($larghezza / 5, $styleCell, $styleTable);
            $sx15->addText('Note:', $fontBold, 'tdStyle');
            $dx15 = $tableReport->addCell(4 * $larghezza / 5, $styleCell, $styleTable);
            $dx15->addText($report->getNote(), $fontNormal, 'tdStyle');
        }

        // Save File
        $writer = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');

        $response = new StreamedResponse(
                function () use ($writer) {
            $writer->save('php://output');
        }, 200, array()
        );

        /* @var $response \Symfony\Component\HttpFoundation\Response */
        $response->headers->set('Content-Type', 'application/rtfn/vnd.openxmlformats-officedocument.wordprocessingml.document; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=report_' . \Doctrine\Common\Util\Inflector::camelize($pratica->getClaimant()) . date('_d-m-Y') . '.docx;');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->sendHeaders();
        return $response;
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
