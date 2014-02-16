<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;
use Ephp\UtilityBundle\Utility\String;
use Claims\HAuditBundle\Entity\Audit;
use Claims\HAuditBundle\Entity\Gruppo;
use Claims\HAuditBundle\Entity\Pratica;
use Claims\HAuditBundle\Entity\Question;
use Claims\HAuditBundle\Entity\Sottogruppo;
use Claims\HAuditBundle\Entity\AuditQuestion;
use Claims\HAuditBundle\Entity\PraticaQuestion;

/**
 * @Route("/sync/claims-h-audit")
 */
class SyncController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\CurlController;

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-list/push", name="sync_claims-h-audit-push-list", options={"_format": "json"})
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
     * @Route("-list/fetch", name="sync_claims-h-audit-fetch-list")
     * @Template()
     */
    public function fetchListAction() {
        $output = json_decode($this->curlGet($this->container->getParameter('jf.server') . '/sync/claims-h-audit-list/push'));
        foreach ($output as $key => $audit) {
            $n = $this->countDql('ClaimsHAuditBundle:Audit', array('remoteId' => $audit->id));
            $output[$key]->imported = $n;
        }

        return array('entities' => $output);
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-audit/{id}/fetch", name="sync_claims-h-audit-fetch-audit", options={"_format": "json"})
     */
    public function fetchAuditAction($id) {
        set_time_limit(3600);

        $output = json_decode($this->curlGet($this->container->getParameter('jf.server') . '/sync/claims-h-audit-audit/' . $id . '/push'));

        try {
            $this->getEm()->beginTransaction();
            $audit = $this->findOneBy('ClaimsHAuditBundle:Audit', array('remoteId' => $output->audit->id));
            /* @var $audi Audit */
            if (!$audit) {
                $audit = new Audit();
                $audit->setRemoteId($output->audit->id);
            }
            $audit->setLuogo($output->audit->luogo);
            $audit->setGiorno(\DateTime::createFromFormat('d/m/Y', $output->audit->giorno));
            $audit->setNote($output->audit->note);
            $this->persist($audit);

            foreach ($output->question as $_question) {
                $question = $this->findOneBy('ClaimsHAuditBundle:Question', array('remoteId' => $_question->id));
                /* @var $question Question */
                if (!$question) {
                    $question = new Question();
                    $question->setRemoteId($_question->id);
                }

                if ($_question->gruppo) {
                    $gruppo = $this->findOneBy('ClaimsHAuditBundle:Gruppo', array('remoteId' => $_question->gruppo->id));
                    if (!$gruppo) {
                        $gruppo = new Gruppo();
                        $gruppo->setRemoteId($_question->gruppo->id);
                        $gruppo->setOrdine($_question->gruppo->ordine);
                        $gruppo->setShow($_question->gruppo->show);
                        $gruppo->setTitle($_question->gruppo->title);
                        $gruppo->setTitolo($_question->gruppo->titolo);
                        $this->persist($gruppo);
                    }
                    $question->setGruppo($gruppo);
                }

                if ($_question->sottogruppo) {
                    $sottogruppo = $this->findOneBy('ClaimsHAuditBundle:Sottogruppo', array('remoteId' => $_question->sottogruppo->id));
                    if (!$sottogruppo) {
                        $sottogruppo = new sottogruppo();
                        $sottogruppo->setRemoteId($_question->sottogruppo->id);
                        $sottogruppo->setMultiplo($_question->sottogruppo->multiplo);
                        $sottogruppo->setTitle($_question->sottogruppo->title);
                        $sottogruppo->setTitolo($_question->sottogruppo->titolo);
                        $this->persist($sottogruppo);
                    }
                    $question->setSottogruppo($sottogruppo);
                }

                $question->setDomanda($_question->domanda);
                $question->setQuestion($_question->question);
                $question->setEsempio($_question->esempio);
                $question->setExample($_question->example);
                $question->setOptions($_question->options);
                $question->setOrdine($_question->ordine);
                $question->setRicerca($_question->ricerca);
                $question->setPrePopulate($_question->prePopulate);
                $question->setType($_question->type);
                $this->persist($question);
            }

            foreach ($audit->getQuestion() as $aq) {
                $this->remove($aq);
            }
            foreach ($output->auditQuestion as $_question) {
                $aq = new AuditQuestion();
                $aq->setAudit($audit);
                $question = $this->findOneBy('ClaimsHAuditBundle:Question', array('remoteId' => $_question));
                /* @var $question Question */
                $aq->setQuestion($question);
                $aq->setOrdine($question->getOrdine());
                if ($question->getGruppo()) {
                    $aq->setGruppo($question->getGruppo());
                }
                if ($question->getSottogruppo()) {
                    $aq->setSottogruppo($question->getSottogruppo());
                }
                $this->persist($aq);
            }

            foreach ($output->pratiche as $_pratica) {
                $pratica = $this->findOneBy('ClaimsHAuditBundle:Pratica', array('remoteId' => $_pratica->id));
                /* @var $pratica Pratica */
                if (!$pratica) {
                    $pratica = new Pratica();
                    $pratica->setRemoteId($_pratica->id);
                }
                $pratica->setSre($_pratica->sre);
                $pratica->setOspedale($_pratica->ospedale);
                $pratica->setMfRef($_pratica->mfRef);
                $pratica->setDol(\DateTime::createFromFormat('d/m/Y', $_pratica->dol));
                $pratica->setDon(\DateTime::createFromFormat('d/m/Y', $_pratica->don));
                $pratica->setCodice($_pratica->codice);
                $pratica->setGruppo($_pratica->gruppo);
                $pratica->setTpa($_pratica->tpa);
                $pratica->setDsCode($_pratica->dsCode);
                $pratica->setClaimant($_pratica->claimant);
                $pratica->setStatus($_pratica->status);
                $pratica->setReserve($_pratica->reserve);
                $pratica->setProReserve($_pratica->proReserve);
                $pratica->setIndemnityCtpPaid($_pratica->indemnityCtpPaid);
                $pratica->setFact($_pratica->fact);
                $pratica->setLiability($_pratica->liability);
                $pratica->setQuantum($_pratica->quantum);
                $pratica->setCronology($_pratica->cronology);
                $pratica->setClaimsHandling($_pratica->claimsHandling);
                $pratica->setCommentsLLR($_pratica->commentsLLR);
                $pratica->setNlComments($_pratica->nlComments);
                $pratica->setNote($_pratica->note);
                $pratica->setDataImport(new \DateTime());
                $pratica->setAudit($audit);
                $this->persist($pratica);

                foreach ($pratica->getQuestion() as $pq) {
                    $this->remove($pq);
                }
                foreach ($_pratica->question as $_question) {
                    $pq = new PraticaQuestion();
                    $pq->setPratica($pratica);
                    $question = $this->findOneBy('ClaimsHAuditBundle:Question', array('remoteId' => $_question->question));
                    if (!$question) {
                        continue;
                    }
                    /* @var $question Question */
                    $pq->setQuestion($question);
                    $pq->setOrdine($_question->ordine);
                    $pq->setResponse($_question->response);
                    if ($question->getGruppo()) {
                        $pq->setGruppo($question->getGruppo());
                    }
                    if ($question->getSottogruppo()) {
                        $pq->setSottogruppo($question->getSottogruppo());
                    }
                    $this->persist($pq);
                }
            }
            $this->getEm()->commit();
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            throw $ex;
        }
        return $this->redirect($this->generateUrl('claims-h-audit_show', array('id' => $audit->getId())));
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-audit/{id}/push", name="sync_claims-h-audit-push-audit", options={"_format": "json"})
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     */
    public function pushAuditAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        $output = array(
            'audit' => array(),
            'question' => array(),
            'auditQuestion' => array(),
            'pratiche' => array(),
        );

        $output['audit'] = array(
            'id' => $entity->getId(),
            'luogo' => $entity->getLuogo(),
            'giorno' => $entity->getGiorno()->format('d/m/Y'),
            'note' => $entity->getNote(),
        );

        foreach ($entity->getQuestion() as $_question) {
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
            $output['auditQuestion'][] = $_question->getQuestion()->getId();
        }
        foreach ($entity->getPratiche() as $pratica) {
            /* @var $pratica \Claims\HAuditBundle\Entity\Pratica */
            $risposte = array();
            foreach ($pratica->getQuestion() as $_question) {
                /* @var $_question \Claims\HAuditBundle\Entity\PraticaQuestion */
                $risposte[] = array(
                    'question' => $_question->getQuestion()->getId(),
                    'ordine' => $_question->getOrdine(),
                    'response' => $_question->getResponse(),
                );
            }
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
                'question' => $risposte,
            );
        }

        return $this->jsonResponse($output);
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-audit/{id}/pull", name="sync_claims-h-audit-pull-audit", options={"_format": "json"})
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     */
    public function pullAuditAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        $output = array(
            'audit' => array(),
            'pratiche' => array(),
        );

        $output['audit'] = array(
            'id' => $entity->getRemoteId(),
            'luogo' => $entity->getLuogo(),
            'giorno' => $entity->getGiorno()->format('d/m/Y'),
            'note' => $entity->getNote(),
        );

        foreach ($entity->getPratiche() as $pratica) {
            /* @var $pratica \Claims\HAuditBundle\Entity\Pratica */
            $risposte = array();
            foreach ($pratica->getQuestion() as $_question) {
                /* @var $_question \Claims\HAuditBundle\Entity\PraticaQuestion */
                $risposte[] = array(
                    'question' => $_question->getQuestion()->getRemoteId(),
                    'ordine' => $_question->getOrdine(),
                    'response' => $_question->getResponse(),
                );
            }
            $output['pratiche'][] = array(
                'id' => $pratica->getRemoteId(),
                'fact' => $pratica->getFact(),
                'liability' => $pratica->getLiability(),
                'quantum' => $pratica->getQuantum(),
                'cronology' => $pratica->getCronology(),
                'claimsHandling' => $pratica->getClaimsHandling(),
                'commentsLLR' => $pratica->getCommentsLLR(),
                'nlComments' => $pratica->getNlComments(),
                'note' => $pratica->getNote(),
                'question' => $risposte,
            );
        }

        return $this->jsonResponse($output);
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-pratica/{id}/pull", name="sync_claims-h-audit-pull-p", options={"_format": "json"})
     * @ParamConverter("id", class="ClaimsHAuditBundle:Pratica")
     */
    public function pullPraticaAction(Pratica $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }

        $risposte = array();
        foreach ($entity->getQuestion() as $_question) {
            /* @var $_question \Claims\HAuditBundle\Entity\PraticaQuestion */
            $risposte[] = array(
                'question' => $_question->getQuestion()->getRemoteId(),
                'ordine' => $_question->getOrdine(),
                'response' => $_question->getResponse(),
            );
        }
        $params = array(
            'fact' => $entity->getFact(),
            'liability' => $entity->getLiability(),
            'quantum' => $entity->getQuantum(),
            'cronology' => $entity->getCronology(),
            'claimsHandling' => $entity->getClaimsHandling(),
            'commentsLLR' => $entity->getCommentsLLR(),
            'nlComments' => $entity->getNlComments(),
            'note' => $entity->getNote(),
            'question' => $risposte,
        );

        $output = json_decode($this->curlPost($this->container->getParameter('jf.server') . '/sync/claims-h-audit-pratica/' . $entity->getRemoteId() . '/get', $params));
        
        return $this->jsonResponse($output);
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-pratica/{id}/get", name="sync_claims-h-audit-get-p", options={"_format": "json"})
     * @ParamConverter("id", class="ClaimsHAuditBundle:Pratica")
     */
    public function getPraticaAction(Pratica $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }

        return $this->jsonResponse($this->getRequest()->getContent());
        
        $risposte = array();
        foreach ($entity->getQuestion() as $_question) {
            /* @var $_question \Claims\HAuditBundle\Entity\PraticaQuestion */
            $risposte[] = array(
                'question' => $_question->getQuestion()->getRemoteId(),
                'ordine' => $_question->getOrdine(),
                'response' => $_question->getResponse(),
            );
        }
        $output = array(
            'id' => $entity->getRemoteId(),
            'fact' => $entity->getFact(),
            'liability' => $entity->getLiability(),
            'quantum' => $entity->getQuantum(),
            'cronology' => $entity->getCronology(),
            'claimsHandling' => $entity->getClaimsHandling(),
            'commentsLLR' => $entity->getCommentsLLR(),
            'nlComments' => $entity->getNlComments(),
            'note' => $entity->getNote(),
            'question' => $risposte,
        );

        return $this->jsonResponse($output);
    }

}
