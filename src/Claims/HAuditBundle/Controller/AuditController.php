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
 * Audit controller.
 *
 * @Route("/claims-h-audit")
 */
class AuditController extends Controller {

    use BaseController;

    /**
     * Lists all Audit entities.
     *
     * @Route("/", name="claims-h-audit")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $entities = $this->findAll('ClaimsHAuditBundle:Audit');

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Audit entity.
     *
     * @Route("/", name="claims-h-audit_create")
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

        $form->add('submit', 'submit', array('label' => 'Continua', 'attr' => array('class' => 'btn')));

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
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     * @Template()
     */
    public function showAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-delete/{id}", name="claims-h-audit_delete")
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
     * @Route("-risposte/{id}", name="claims-h-audit_risposte")
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     * @Template()
     */
    public function risposteAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-salvarisposta", name="claims-h-audit-risposta", options={"expose": true})
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
     * @Route("-salvarisposta", name="claims-h-audit-risposte", options={"expose": true})
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
        foreach ($req as $qid => $value) {
            $pq = $pratica->getValue($qid);
            if (!$pq) {
                $pq = new \Claims\HAuditBundle\Entity\PraticaQuestion();
                $pq->setPratica($pratica);
                $question = $this->find('ClaimsHAuditBundle:Question', $qid);
                $pq->setQuestion($question);
                $pq->setOrdine(0);
                if($question->getSottogruppo()) {
                    $pq->setGruppo($question->getGruppo());
                }
                if($question->getSottogruppo()) {
                    $pq->setSottogruppo($question->getSottogruppo());
                }
            }
            if (is_array($req['value'])) {
                $pq->setResponse(json_encode($req['value']));
            } else {
                $pq->setResponse($req['value']);
            }
            $this->persist($pq);
            $pratica->addQuestion($pq);
        }

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
     * @Route("-q/{id}/{ordine}/{pratica}", name="claims-h-audit-get-risposta", options={"expose": true})
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
     * @Route("-g/{id}/{ordine}/{pratica}", name="claims-h-audit-get-risposte", options={"expose": true})
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

        $group = $audit->getGroup($ordine + 1);

        return array(
            'audit' => $audit,
            'pratica' => $p,
            'group' => $group,
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-show/{id}/{slug}", name="claims-h-audit_show-pratica")
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
     * @Route("/{id}/edit", name="claims-h-audit_edit")
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
     * @Route("/{id}", name="claims-h-audit_update")
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
     * @Route("/{id}/questions", name="claims-h-audit-questions")
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
     * @Route("/{id}/questions", name="claims-h-audit-question-save")
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
     * @Route("-autoupdate-pratica/{slug}", name="claims-h-audit-autoupdate", options={"expose": true}, defaults={"_format": "json"})
     */
    public function autoupdatePraticaAction($slug) {
        $req = $this->getParam('pratica');

        $pratica = $this->findOneBy('ClaimsHAuditBundle:Pratica', array('slug' => $slug));
        /* @var $pratica Pratica */
        try {
            $this->getEm()->beginTransaction();
            $fx = \Doctrine\Common\Util\Inflector::camelize("set_{$req['field']}");
            $pratica->$fx($req['value']);
            $this->persist($pratica);
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            throw $e;
        }
        return new \Symfony\Component\HttpFoundation\Response(json_encode($req));
    }

    /**
     * @Route("-form/{audit}", name="claims-h-audit-file")
     * @Template()
     */
    public function formAction($audit) {
        $entity = $this->find('ClaimsHAuditBundle:Audit', $audit);
        return array('audit' => $audit, 'entity' => $entity);
    }

    /**
     * @Route("-callback", name="claims-h-audit-file-callback", options={"expose": true})
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
