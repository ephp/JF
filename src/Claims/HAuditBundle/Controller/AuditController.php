<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;
use Ephp\UtilityBundle\Utility\String;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Claims\HAuditBundle\Entity\Pratica;
use Claims\HAuditBundle\Entity\Audit;
use Claims\HAuditBundle\Entity\Question;
use Claims\HAuditBundle\Form\AuditType;
use Claims\HAuditBundle\Form\QuestionType;

/**
 * Audit controller
 *
 * @Route("/claims-h-audit/soft")
 */
class AuditController extends Controller {

    use BaseController,
        Traits\AuditController;

    /**
     * Lists all Audit entities.
     *
     * @Route("/", name="claims-h-audit-slc", options={"ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $entities = $this->findAll('ClaimsHAuditBundle:Audit');
        $questions = $this->findBy('ClaimsHAuditBundle:Question', array('somma' => true));
        $slc = array();
        foreach ($questions as $question) {
            /* @var $question Question */
            $q = "
select sum(replace(r.response, ',', '')) as tot,
       count(*) as n
  from claims_h_audit_pratica_question r 
 where r.question_id = :id
   and r.response != ''
";
            $rows = $this->executeSql($q, array('id' => $question->getId()));

            if ($rows[0]['n']) {
                $slc[] = array(
                    'question' => $question,
                    'tot' => $rows[0]['tot'],
                    'n' => $rows[0]['n'],
                );
            }
        }

        $gestori = $this->getRepository('JFACLBundle:Gestore')
                ->createQueryBuilder('u')
                ->where('u.roles LIKE :audit')
                ->setParameter('audit', '%"C_AUDIT_H"%')
                ->andWhere('u.sigla != :eph')
                ->setParameter('eph', 'EPH')
                ->getQuery()
                ->execute();

        $priorita = $this->findBy('ClaimsCoreBundle:Priorita', array('area' => 'audit', 'show' => true));

        return array(
            'gestori' => $gestori,
            'priorita' => $priorita,
            'entities' => $entities,
            'slc' => $slc,
        );
    }

    /**
     * Creates a new Audit entity.
     *
     * @Route("/", name="claims-h-audit_create", options={"ACL": {"in_role": {"C_AUDIT_HC"}}})
     * @Method("POST")
     * @Template("ClaimsHAuditBundle:Audit:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Audit();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setCliente($this->getUser()->getCliente());
            $this->persist($entity);

            return $this->redirect($this->generateUrl('claims-h-audit-file', array('audit' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Audit entity.
     *
     * @param Audit $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Audit $entity) {
        $form = $this->createForm(new AuditType(), $entity, array(
            'action' => $this->generateUrl('claims-h-audit_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Continue', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Displays a form to create a new Audit entity.
     *
     * @Route("/new", name="claims-h-audit_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Audit();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("/{id}", name="claims-h-audit_show")
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit", options={"ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Template()
     */
    public function showAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        $questions = $this->findBy('ClaimsHAuditBundle:Question', array('somma' => true));
        $slc = array();
        foreach ($questions as $question) {
            /* @var $question Question */
            $q = "
select sum(replace(r.response, ',', '')) as tot,
       count(*) as n
  from claims_h_audit_pratica_question r 
  left join claims_h_audit_pratiche p on p.id = r.pratica_id
 where r.question_id = :id
   and p.audit_id = :aid
   and r.response != ''
";
            $rows = $this->executeSql($q, array('id' => $question->getId(), 'aid' => $entity->getId()));

            if ($rows[0]['n']) {
                $slc[] = array(
                    'question' => $question,
                    'tot' => $rows[0]['tot'],
                    'n' => $rows[0]['n'],
                );
            }
        }

        $sorting = $this->sorting();
        $dati = $this->getUser()->getDati();
        $pratiche = $this->getRepository('ClaimsHAuditBundle:Pratica')->ricerca($entity, $this->getParam('ricerca', array()), $dati['claims_haudit_sorting']);

        $gestori = $this->getRepository('JFACLBundle:Gestore')
                ->createQueryBuilder('u')
                ->where('u.roles LIKE :audit')
                ->setParameter('audit', '%"C_AUDIT_H"%')
                ->andWhere('u.sigla != :eph')
                ->setParameter('eph', 'EPH')
                ->getQuery()
                ->execute();

        $priorita = $this->findBy('ClaimsCoreBundle:Priorita', array('area' => 'audit', 'show' => true));

        return array(
            'gestori' => $gestori,
            'priorita' => $priorita,
            'entity' => $entity,
            'slc' => $slc,
            'pratiche' => $pratiche,
            'sorting' => $sorting,
            'query' => $this->getQuery(),
            'route' => $this->generateUrl('claims-h-audit_risposte', array('id' => $entity->getId())),
            'ricerca' => $this->getParam('ricerca', array()),
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-all/", name="claims-h-audit_show_all", options={"ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Method("GET")
     * @Template("ClaimsHAuditBundle:Audit:show.html.twig")
     */
    public function showAllAction() {
        $sorting = $this->sorting();
        $dati = $this->getUser()->getDati();
        $pratiche = $this->getRepository('ClaimsHAuditBundle:Pratica')->ricerca(null, $this->getParam('ricerca', array()), $dati['claims_haudit_sorting']);

        $entity = new Audit();
        $entity->setLuogo('All Audit');
        $entity->setGiorno(new \DateTime());

        $gestori = $this->getRepository('JFACLBundle:Gestore')
                ->createQueryBuilder('u')
                ->where('u.roles LIKE :audit')
                ->setParameter('audit', '%"C_AUDIT_H"%')
                ->andWhere('u.sigla != :eph')
                ->setParameter('eph', 'EPH')
                ->getQuery()
                ->execute();

        $priorita = $this->findBy('ClaimsCoreBundle:Priorita', array('area' => 'audit', 'show' => true));

        return array(
            'gestori' => $gestori,
            'priorita' => $priorita,
            'entity' => $entity,
            'pratiche' => $pratiche,
            'sorting' => $sorting,
            'query' => $this->getQuery(),
            'route' => $this->generateUrl('claims-h-audit_show_all'),
            'ricerca' => $this->getParam('ricerca', array()),
            'full' => true,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-risposte/{id}", name="claims-h-audit_risposte", options={"ACL": {"in_role": {"C_AUDIT_H"}}})
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     * @Template()
     */
    public function risposteAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        $sorting = $this->sorting();
        $dati = $this->getUser()->getDati();
        $pratiche = $this->getRepository('ClaimsHAuditBundle:Pratica')->ricerca($entity, $this->getParam('ricerca', array()), $dati['claims_haudit_sorting']);

        return array(
            'entity' => $entity,
            'pratiche' => $pratiche,
            'sorting' => $sorting,
            'query' => $this->getQuery(),
            'route' => $this->generateUrl('claims-h-audit_risposte', array('id' => $entity->getId())),
            'ricerca' => $this->getParam('ricerca', array()),
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-pratica-delete/{id}", name="claims-h-audit_pratica-delete", options={"ACL": {"in_role": {"C_AUDIT_HC"}}})
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Pratica")
     */
    public function deletePraticaAction(Pratica $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        return $this->redirect($this->generateUrl('claims-h-audit'));
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-pratica-file-delete/{id}/{pratica}", name="claims-h-audit_file-delete", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Method("POST")
     * @Template("ClaimsHAuditBundle:Audit:files.html.twig")
     */
    public function deleteFileAction($id, $pratica) {
        $file = $this->find('JFDragDropBundle:File', $id);
        /* @var $file \JF\DragDropBundle\Entity\File */
        if (!$file) {
            throw $this->createNotFoundException('Unable to find File entity.');
        }
        $p = $this->find('ClaimsHAuditBundle:Pratica', $pratica);
        /* @var $p Pratica */
        if (!$p) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }

        $p->removeDocumenti($file);
        $this->persist($p);

        return array(
            'audit' => $p->getAudit(),
            'pratica' => $p,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-salvarisposta", name="claims-h-audit-risposta", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Template("ClaimsHAuditBundle:Audit:question.html.twig")
     */
    public function rispostaAction() {
        $req = $this->getParam('risposta');
        $audit = $this->find('ClaimsHAuditBundle:Audit', $req['audit']);
        /* @var $audit Audit */
        if (!$audit) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        $pratica = $this->find('ClaimsHAuditBundle:Pratica', $req['pratica']);
        /* @var $pratica Pratica */
        if (!$pratica) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }
        $pq = $pratica->getValue($req['question']);
        if (!$pq) {
            $pq = new \Claims\HAuditBundle\Entity\PraticaQuestion();
            $pq->setPratica($pratica);
            $pq->setQuestion($this->find('ClaimsHAuditBundle:Question', $req['question']));
            $pq->setOrdine($req['ordine']);
        }
        if (is_array($req['value'])) {
            $pq->setResponse(json_encode($req['value']));
        } else {
            $pq->setResponse($req['value']);
        }
        $this->persist($pq);

        $pratica->addQuestion($pq);

        $question = null;
        foreach ($audit->getQuestion() as $q) {
            if ($q->getOrdine() == intval($req['ordine']) + 1) {
                $question = $q;
            }
        }

        return array(
            'audit' => $audit,
            'pratica' => $pratica,
            'question' => $question,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-delete/{id}", name="claims-h-audit_delete", options={"ACL": {"in_role": {"C_AUDIT_HC"}}})
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     */
    public function deleteAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        if ($this->getUser()->getCliente()->getId() == $entity->getCliente()->getId()) {
            $this->remove($entity);
        }

        return $this->redirect($this->generateUrl('claims-h-audit'));
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-riepilogo/{id}", name="claims-h-audit-riepilogo", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Template("ClaimsHAuditBundle:Audit:showPratica/riepilogo.html.twig")
     */
    public function riepilogoGruppoAction($id) {
        $pratica = $this->find('ClaimsHAuditBundle:Pratica', $id);
        /* @var $pratica Pratica */
        if (!$pratica) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }

        return array(
            'audit' => $pratica->getAudit(),
            'pratica' => $pratica,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-salvarisposte", name="claims-h-audit-risposte", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Template("ClaimsHAuditBundle:Audit:groups.html.twig")
     */
    public function risposteGruppoAction() {
        $req = $this->getParam('risposta');
        $audit = $this->find('ClaimsHAuditBundle:Audit', $this->getParam('audit'));
        /* @var $audit Audit */
        if (!$audit) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        $pratica = $this->find('ClaimsHAuditBundle:Pratica', $this->getParam('pratica'));
        /* @var $pratica Pratica */
        if (!$pratica) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }
        if ($pratica->getPriorita()->getPriorita() == 'To do') {
            $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Work in progress')));
            $this->persist($pratica);
        }
        if (!$pratica->getGestore()) {
            $pratica->setGestore($this->getUser());
            $this->persist($pratica);
        }
        foreach ($req as $qid => $value) {
            if (is_array($value)) {
                $ris = $pratica->getRisposte($qid);
                foreach ($ris as $r) {
                    $this->remove($r);
                }
                foreach ($value as $i => $v) {
                    $pq = new \Claims\HAuditBundle\Entity\PraticaQuestion();
                    $pq->setPratica($pratica);
                    $question = $this->find('ClaimsHAuditBundle:Question', $qid);
                    $pq->setQuestion($question);
                    $pq->setOrdine($i);
                    if ($question->getSottogruppo()) {
                        $pq->setGruppo($question->getGruppo());
                    }
                    if ($question->getSottogruppo()) {
                        $pq->setSottogruppo($question->getSottogruppo());
                    }
                    $pq->setResponse($v);
                    $this->persist($pq);
                    $pratica->addQuestion($pq);
                }
            } else {
                $pq = $pratica->getValue($qid);
                if (!$pq) {
                    $pq = new \Claims\HAuditBundle\Entity\PraticaQuestion();
                    $pq->setPratica($pratica);
                    $question = $this->find('ClaimsHAuditBundle:Question', $qid);
                    $pq->setQuestion($question);
                    $pq->setOrdine(0);
                    if ($question->getSottogruppo()) {
                        $pq->setGruppo($question->getGruppo());
                    }
                    if ($question->getSottogruppo()) {
                        $pq->setSottogruppo($question->getSottogruppo());
                    }
                }
                $pq->setResponse($value);
                $this->persist($pq);
                $pratica->addQuestion($pq);
            }
        }
        if ($pratica->getRemoteId()) {
            $pratica->setPriorita($this->findOneby('ClaimsCoreBundle:Priorita', array('priorita' => 'Aggiornato')));
        }
        $this->persist($pratica);
        $group = $audit->getGroup($this->getParam('ordine') + 1);

        return array(
            'audit' => $audit,
            'pratica' => $pratica,
            'group' => $group,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-q/{id}/{ordine}/{pratica}", name="claims-h-audit-get-risposta", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Template("ClaimsHAuditBundle:Audit:question.html.twig")
     */
    public function questionAction($id, $ordine, $pratica) {
        $audit = $this->find('ClaimsHAuditBundle:Audit', $id);
        /* @var $audit Audit */
        if (!$audit) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        $p = $this->find('ClaimsHAuditBundle:Pratica', $pratica);
        /* @var $p Pratica */
        if (!$p) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }

        $question = null;
        foreach ($audit->getQuestion() as $q) {
            if ($q->getOrdine() == intval($ordine)) {
                $question = $q;
            }
        }

        return array(
            'audit' => $audit,
            'pratica' => $p,
            'question' => $question,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-g/{id}/{ordine}/{pratica}", name="claims-h-audit-get-risposte", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Template("ClaimsHAuditBundle:Audit:groups.html.twig")
     */
    public function groupQuestionsAction($id, $ordine, $pratica) {
        $audit = $this->find('ClaimsHAuditBundle:Audit', $id);
        /* @var $audit Audit */
        if (!$audit) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        $p = $this->find('ClaimsHAuditBundle:Pratica', $pratica);
        /* @var $p Pratica */
        if (!$p) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }

        $group = $audit->getGroup($ordine);

        return array(
            'audit' => $audit,
            'pratica' => $p,
            'group' => $group,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-f/{id}/{pratica}", name="claims-h-audit-files", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Template()
     */
    public function filesAction($id, $pratica) {
        $audit = $this->find('ClaimsHAuditBundle:Audit', $id);
        /* @var $audit Audit */
        if (!$audit) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        $p = $this->find('ClaimsHAuditBundle:Pratica', $pratica);
        /* @var $p Pratica */
        if (!$p) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }

        $file = $this->find('JFDragDropBundle:File', $this->getParam('file_id'));
        $p->addDocumenti($file);
        $this->persist($p);

        return array(
            'audit' => $audit,
            'pratica' => $p,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-show/{id}/{slug}", name="claims-h-audit_show-pratica", options={"ACL": {"in_role": {"C_AUDIT_H"}}})
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     * @Template()
     */
    public function showPraticaAction(Audit $entity, $slug) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        $pratica = $this->findOneBy('ClaimsHAuditBundle:Pratica', array('slug' => $slug));

        return array(
            'pratica' => $pratica,
            'audit' => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Audit entity.
     *
     * @Route("/{id}/edit", name="claims-h-audit_edit", options={"ACL": {"in_role": {"C_AUDIT_HC"}}})
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     * @Template("ClaimsHAuditBundle:Audit:new.html.twig")
     */
    public function editAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Audit entity.
     *
     * @param Audit $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Audit $entity) {
        $form = $this->createForm(new AuditType(), $entity, array(
            'action' => $this->generateUrl('claims-h-audit_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Edits an existing Audit entity.
     *
     * @Route("/{id}", name="claims-h-audit_update", options={"ACL": {"in_role": {"C_AUDIT_HC"}}})
     * @Method("PUT")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     * @Template("ClaimsHAuditBundle:Audit:new.html.twig")
     */
    public function updateAction(Audit $entity) {
        $request = $this->getRequest();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('claims-h-audit-file', array('audit' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Audit entity.
     *
     * @Route("/{id}/questions", name="claims-h-audit-questions", options={"ACL": {"in_role": {"C_AUDIT_HC"}}})
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     * @Template()
     */
    public function questionsAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

//        $questions = $this->findBy('ClaimsHAuditBundle:Question', array('cliente' => $this->getUser()->getCliente()->getId()));
        $questions = $this->findBy('ClaimsHAuditBundle:Question', array('cliente' => null), array('ordine' => 'ASC'));

        if (count($entity->getQuestion()) == 0) {
            foreach ($questions as $_question) {
                /* @var $_question \Claims\HAuditBundle\Entity\Question */
                if ($_question->getOrdine() > 0) {
                    $question = new \Claims\HAuditBundle\Entity\AuditQuestion();
                    $question->setAudit($entity);
                    $question->setOrdine($_question->getOrdine());
                    if ($_question->getGruppo()) {
                        $question->setGruppo($_question->getGruppo());
                    }
                    if ($_question->getSottogruppo()) {
                        $question->setSottogruppo($_question->getSottogruppo());
                    }
                    $question->setQuestion($_question);
                    $this->persist($question);
                }
            }
        }

        return $this->redirect($this->generateUrl('claims-h-audit_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a form to edit a Audit entity.
     *
     * @param Audit $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createQuestionForm(Question $entity, Audit $audit) {
        $form = $this->createForm(new QuestionType(), $entity, array(
            'action' => $this->generateUrl('claims-h-audit-question-save', array('id' => $audit->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'button', array('label' => 'Aggiungi', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Edits an existing Audit entity.
     *
     * @Route("/{id}/questions", name="claims-h-audit-question-save", options={"ACL": {"in_role": {"C_AUDIT_HC"}}})
     * @Method("POST")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     * @Template("ClaimsHAuditBundle:Audit:questions/question.html.twig")
     */
    public function saveQuestionAction(Audit $audit) {
        $request = $this->getRequest();
        if (!$audit) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        $entity = new Question();
        $form = $this->createQuestionForm($entity, $audit);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entity->setCliente($this->getUser()->getCliente());
            $options = explode("\n", $entity->getOptions());
            foreach ($options as $id => $option) {
                $options[$id] = trim($option);
            }
            $entity->setOptions($options);
            $this->persist($entity);

            return array('question' => $entity);
        }

        throw new \Exception($form->getErrorsAsString());
    }

    /**
     * @Route("-autoupdate-pratica/{slug}", name="claims-h-audit-autoupdate", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}}, defaults={"_format": "json"})
     */
    public function autoupdatePraticaAction($slug) {
        $req = $this->getParam('pratica');

        $pratica = $this->findOneBy('ClaimsHAuditBundle:Pratica', array('slug' => $slug));
        /* @var $pratica Pratica */
        if (!$pratica) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }

        if ($pratica->getPriorita()->getPriorita() == 'To do') {
            $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Work in progress')));
        }

        /* @var $pratica Pratica */
        try {
            $this->getEm()->beginTransaction();
            $fx = \Doctrine\Common\Util\Inflector::camelize("set_{$req['field']}");
            $pratica->$fx($req['value']);
            if ($pratica->getRemoteId()) {
                $pratica->setPriorita($this->findOneby('ClaimsCoreBundle:Priorita', array('priorita' => 'Aggiornato')));
            }
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        return new \Symfony\Component\HttpFoundation\Response(json_encode($req));
    }

    /**
     * @Route("-form/{audit}", name="claims-h-audit-file", options={"ACL": {"in_role": {"C_AUDIT_HC"}}})
     * @Template()
     */
    public function formAction($audit) {
        $entity = $this->find('ClaimsHAuditBundle:Audit', $audit);
        return array('audit' => $audit, 'entity' => $entity);
    }

    /**
     * @Route("-callback", name="claims-h-audit-file-callback", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_HC"}}})
     * @Template()
     */
    public function callbackAction() {
        set_time_limit(3600);
        $source = __DIR__ . '/../../../../web' . $this->getParam('file');
        $audit = $this->find('ClaimsHAuditBundle:Audit', $this->getParam('audit'));
        /*
          if (intval($this->getParam('add_more', 1)) == 0) {
          $this->getRepository('ClaimsHAuditBundle:Pratica')->cancellaAudit($audit);
          }
         */
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
                    if (isset($valori_riga[1]) && in_array(strtoupper($valori_riga[1]), array('SRE'))) {
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
                            $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'To do', 'area' => 'audit')));
                            $pratica->setSemaforo($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'OFF', 'area' => 'audit-v')));
                            $pratica->setStatoPratica($this->findOneBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $cliente->getId(), 'primo' => true)));
                            foreach ($valori_riga as $idx => $value) {
                                if (!isset($colonne[$idx])) {
                                    continue;
                                }
                                switch (strtoupper($colonne[$idx])) {
                                    case 'TPA  REF.':
                                    case 'TPA REF.':
                                    case 'TPA REF':
                                        $pratica->setTpa($value);
                                        $pratica->setCodice($value);
                                        break;

                                    case 'TPA':
                                    case 'GROUP':
                                    case 'GRUPPO':
                                        $pratica->setGruppo($value);
                                        break;

                                    case 'MONTH':
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

                                    case 'STATUS':
                                        $pratica->setStatus($value);
                                        break;

                                    case 'SRE':
                                        $pratica->setSre($value);
                                        break;

                                    case 'INDEMNITY + CTP PAID':
                                    case 'INDEMNITY+ CTP PAID':
                                    case 'INDEMNITY +CTP PAID':
                                    case 'INDEMNITY+CTP PAID':
                                        $pratica->setIndemnityCtpPaid(String::currency($value, ',', '.'));
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

    /**
     * @Route("-cambia-priorita/", name="claims_hospital_audit_cambia_priorita", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}}, defaults={"_format": "json"})
     */
    public function cambiaPrioritaAction() {
        $req = $this->getParam('priorita');

        $pratica = $this->findOneBy('ClaimsHAuditBundle:Pratica', array('slug' => $req['id']));
        /* @var $pratica Pratica */
        $priorita = $this->find('ClaimsCoreBundle:Priorita', $req['priorita']);
        /* @var $priorita Priorita */

        try {
            $this->getEm()->beginTransaction();
            $pratica->setPriorita($priorita);
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        return $this->jsonResponse(array('id' => $priorita->getId(), 'label' => $priorita->getPriorita(), 'css' => $priorita->getCss()));
    }

    /**
     * @Route("-cambia-semaforo/", name="claims_hospital_audit_cambia_semaforo", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_HV"}}}, defaults={"_format": "json"})
     */
    public function cambiaSemaforoAction() {
        $req = $this->getParam('semaforo');

        $pratica = $this->findOneBy('ClaimsHAuditBundle:Pratica', array('slug' => $req['id']));
        /* @var $pratica Pratica */
        $priorita = $this->find('ClaimsCoreBundle:Priorita', $req['semaforo']);
        /* @var $priorita Priorita */

        try {
            $this->getEm()->beginTransaction();
            $pratica->setSemaforo($priorita);
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        return $this->jsonResponse(array('id' => $priorita->getId(), 'label' => $priorita->getPriorita(), 'css' => $priorita->getCss()));
    }

    /**
     * @Route("-cambia-gestore/", name="claims_hospital_audit_cambia_gestore", options={"expose": true, "ACL": {"in_role": {"C_AUDIT_H"}}}, defaults={"_format": "json"})
     */
    public function cambiaGestoreAction() {
        $req = $this->getParam('gestore');

        $pratica = $this->findOneBy('ClaimsHAuditBundle:Pratica', array('slug' => $req['id']));
        /* @var $pratica Pratica */
        $gestore = $this->findOneBy('JFACLBundle:Gestore', array('slug' => $req['gestore']));
        /* @var $gestore Gestore */

        $genera = false;
        try {
            $this->getEm()->beginTransaction();
            if ($gestore) {
                if (in_array($pratica->getPriorita()->getPriorita(), array('Nuovo', 'To do', 'Updated'))) {
                    $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('area' => 'audit', 'priorita' => 'Work in progress')));
                }
            } else {
                $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('area' => 'audit', 'priorita' => 'To do')));
            }
            $pratica->setGestore($gestore);
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        $priorita = $pratica->getPriorita();
        if (!$gestore) {
            $gestore = new \JF\ACLBundle\Entity\Gestore();
            $gestore->setNome('');
            $gestore->setSigla('');
            $gestore->setSlug('');
        }
        return $this->jsonResponse(array('nome' => $gestore->getNome(), 'slug' => $gestore->getSlug(), 'sigla' => $gestore->getSigla(), 'id' => $priorita->getId(), 'label' => $priorita->getPriorita(), 'css' => $priorita->getCss()));
    }

}
