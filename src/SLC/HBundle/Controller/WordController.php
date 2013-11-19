<?php

namespace SLC\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;
use Ephp\UtilityBundle\Utility\String;
use Ephp\UtilityBundle\PhpWord\HtmlConverter;

/**
 * @Route("/slc/claims-hospital-word")
 */
class WordController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController,
        \Claims\HBundle\Controller\Traits\TabelloneController,
        Traits\TabelloneController;

    /**
     * @Route("-word-report/{slug}", name="claims_hospital_word_report", options={"expose": true, "ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     */
    public function wordReportAction($slug) {
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

        $word = new HtmlConverter("Report ".$pratica);
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

}
