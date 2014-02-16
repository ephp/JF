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
 * @Route("/sync/claims-h-audit")
 */
class SyncController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\CurlController;

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-push-list", name="sync_claims-h-audit-push-list", options={"_format": "json"})
     */
    public function pushListAction() {
        $output = array();
        foreach ($this->findAll('ClaimsHAuditBundle:Audit') as $audit) {
            /* @var $audit Audit */
            $output[] = array(
                'id' => $audit->getId(),
                'luogo' => $audit->getLuogo(),
                'giorno' => $audit->getGiorno()->format('d/m/Y'),
                'note' => $audit->getNote(),
                'pratiche' => count($audit->getPratiche()),
                'domande' => count($audit->getQuestion()),
            );
        }

        return $this->jsonResponse($output);
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-fetch-list", name="sync_claims-h-audit-fetch-list")
     */
    public function fetchListAction() {
        $output = $this->curlGet($this->container->getParameter('jf.server').'/sync/claims-h-audit-push-list');
        foreach($output as $key => $audit) {
            $output[$key]['imported'] = $this->countDql('ClaimsHAuditBundle:Audit', array('serverId' => $audit['id']));
        }

        return array('audit' => $output);
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-fetch-audit/{id}", name="sync_claims-h-audit-fetch-audit", options={"_format": "json"})
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     */
    public function fetchAuditAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        $output = $this->curlGet($this->container->getParameter('jf.server').'/sync/claims-h-audit-push-audit/'.$entity->getId());

        return $response;
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-push-audit/{id}", name="sync_claims-h-audit-push-audit", options={"_format": "json"})
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     */
    public function pushAuditAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        $output = array(
            'audit' => array(),
            'question' => array(),
            'audit-question' => array(),
            'pratiche' => array(),
            'pratiche-question' => array(),
        );
        
        $output['audit'] = array(
            'id' => $entity->getId(),
            'luogo' => $entity->getLuogo(),
            'giorno' => $entity->getGiorno()->format('d/m/Y'),
            'note' => $entity->getNote(),
        );
        
        foreach($entity->getQuestion() as $_question) {
            /* @var $_question \Claims\HAuditBundle\Entity\AuditQuestion */
            $question = $_question->getQuestion();
            /* @var $question \Claims\HAuditBundle\Entity\Question */
            $output['question'][] = array(
                'id' => $question->getId(),
                'gruppo' => $question->getGruppo() ? array(
                    'id' => $question->getGruppo()->getId(),
                    'ordine' => $question->getGruppo()->getOrdine(),
                    'show' => $question->getGruppo()->getShow(),
                    'title' => $question->getGruppo()->getTitle(),
                    'titolo' => $question->getGruppo()->getTitolo(),
                ) : null,
                'sottogruppo' => $question->getSottogruppo() ? array(
                    'id' => $question->getSottogruppo()->getId(),
                    'multiplo' => $question->getSottogruppo()->getMultiplo(),
                    'title' => $question->getSottogruppo()->getTitle(),
                    'titolo' => $question->getSottogruppo()->getTitolo(),
                ) : null,
                'domanda' => $question->getDomanda(),
                'question' => $question->getQuestion(),
                'esempio' => $question->getEsempio(),
                'example' => $question->getExample(),
                'options' => $question->getOptions(),
                'ordine' => $question->getOrdine(),
                'prePopulate' => $question->getPrePopulate(),
                'ricerca' => $question->getRicerca(),
                'type' => $question->getType(),
            );
            $output['audit-question'][] = array(
                'question' => $_question->getQuestion()->getId(),
                'ordine' => $_question->getOrdine(),
            );
        }
        foreach($entity->getPratiche() as $pratica) {
            /* @var $pratica \Claims\HAuditBundle\Entity\Pratica */
            $output['pratiche'][] = array(
                'id' => $pratica->getId(),
                'sre' => $pratica->getSre(),
                'ospedale' => $pratica->getOspedale(),
                'mfRef' => $pratica->getMfRef(),
                'dol' => $pratica->getDol()->format('d/m/Y'),
                'don' => $pratica->getDon()->format('d/m/Y'),
                'codice' => $pratica->getCodice(),
                'gruppo' => $pratica->getGruppo(),
                'tpa' => $pratica->getTpa(),
                'dsCode' => $pratica->getDsCode(),
                'claimant' => $pratica->getClaimant(),
                'status' => $pratica->getStatus(),
                'reserve' => $pratica->getReserve(),
                'proReserve' => $pratica->getProReserve(),
                'indemnityCtpPaid' => $pratica->getIndemnityCtpPaid(),
                'fact' => $pratica->getFact(),
                'liability' => $pratica->getLiability(),
                'quantum' => $pratica->getQuantum(),
                'cronology' => $pratica->getCronology(),
                'claimsHandling' => $pratica->getClaimsHandling(),
                'commentsLLR' => $pratica->getCommentsLLR(),
                'nlComments' => $pratica->getNlComments(),
                'note' => $pratica->getNote(),
            );
            
            foreach($pratica->getQuestion() as $_question) {
                /* @var $_question \Claims\HAuditBundle\Entity\PraticaQuestion */
                $output['audit-question'][] = array(
                    'pratica' => $pratica->getId(),
                    'question' => $_question->getQuestion()->getId(),
                    'ordine' => $_question->getOrdine(),
                    'response' => $_question->getResponse(),
                );
            }
        }
        
        return $this->jsonResponse($output);
    }
}
