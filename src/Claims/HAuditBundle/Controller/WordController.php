<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;
use Ephp\UtilityBundle\Utility\String;
use Ephp\UtilityBundle\PhpWord\HtmlConverter;

/**
 * @Route("/claims-audit-hospital-word")
 */
class WordController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController;

    /**
     * @Route("-report/{slug}", name="claims_audit_hospital_word_report", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}})
     */
    public function wordReportAction($slug) {
        $pratica = $this->findOneBy('ClaimsHAuditBundle:Pratica', array('slug' => $slug));
        /* @var $pratica \Claims\HBundle\Entity\Pratica */

        if ($this->getUser()->getCliente()->getId() != $pratica->getCliente()->getId()) {
            return $this->createNotFoundException('Utente non autorizzato');
        }

        $twig = 'JFAuditHBundle:Word:wordReport.html.twig';

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
