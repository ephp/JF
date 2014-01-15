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
     * @Route("/{id}/save-questions", name="claims-h-audit-save-questions", options={"expose": true}, defaults={"_format": "json"})
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     */
    public function saveQuestionsAction(Audit $entity) {
        foreach ($entity->getQuestion() as $qa) {
            $this->remove($qa);
        }
        foreach($this->getParam('ids') as $i => $id) {
            $q = $this->find('ClaimsHAuditBundle:Question', $id);
            $qa = new \Claims\HAuditBundle\Entity\AuditQuestion();
            $qa->setAudit($entity);
            $qa->setQuestion($q);
            $qa->setOrdine($i);
            $this->persist($qa);
        }
        return $this->jsonResponse(array('redirect' => $this->generateUrl('claims-h-audit_show', array('id' => $entity->getId()))));
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

        $oldAudit = $this->findOneBy('ClaimsHAuditBundle:Audit', array('cliente' => $this->getUser()->getCliente()->getId()), array('id' => 'DESC'));
        /* @var $oldAudit Audit */

        $questions = $this->findBy('ClaimsHAuditBundle:Question', array('cliente' => $this->getUser()->getCliente()->getId()));

        
        if (count($entity->getQuestion()) == 0 && $oldAudit) {
            foreach ($oldAudit->getQuestion() as $_question) {
                /* @var $_question \Claims\HAuditBundle\Entity\AuditQuestion */
                $question = new \Claims\HAuditBundle\Entity\AuditQuestion();
                $question->setAudit($entity);
                $question->setOrdine($_question->getOrdine());
                $question->setQuestion($_question->getQuestion());
                $entity->addQuestion($question);
                foreach ($questions as $i => $q) {
                    if($q->getId() == $_question->getQuestion()->getId()) {
                        unset($questions[$i]);
                        break;
                    }
                }
            }
        } else {
            foreach ($entity->getQuestion() as $_question) {
                /* @var $_question \Claims\HAuditBundle\Entity\AuditQuestion */
                foreach ($questions as $i => $q) {
                    if($q->getId() == $_question->getQuestion()->getId()) {
                        unset($questions[$i]);
                        break;
                    }
                }
            }
        }

        $question = new Question();

        $form = $this->createQuestionForm($question, $entity);

        return array(
            'entity' => $entity,
            'questions' => $questions,
            'form' => $form->createView(),
        );
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
            $entity->setOptions(explode("\n", $entity->getOptions()));
            $this->persist($entity);

            return array('question' => $entity);
        }

        throw new \Exception($form->getErrorsAsString());
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
