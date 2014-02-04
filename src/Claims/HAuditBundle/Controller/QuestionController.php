<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Claims\HAuditBundle\Entity\Question;
use Claims\HAuditBundle\Form\QuestionType;

/**
 * Question controller.
 *
 * @Route("/eph/domande-audit")
 */
class QuestionController extends Controller
{
    use BaseController;

    /**
     * Lists all Question entities.
     *
     * @Route("/", name="eph_domande-audit", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $entities = $this->findBy('ClaimsHAuditBundle:Question', array('cliente' => null));
        $gruppi = $this->findAll('ClaimsHAuditBundle:Gruppo');
        return array(
            'entities' => $entities,
            'gruppi' => $gruppi,
        );
    }
    /**
     * Creates a new Question entity.
     *
     * @Route("/", name="eph_domande-audit_create")
     * @Method("POST")
     * @Template("ClaimsHAuditBundle:Question:new.html.twig", options={"ACL": {"in_role": "R_EPH"}})
     */
    public function createAction(Request $request)
    {
        $entity = new Question();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('eph_domande-audit'));

        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Question entity.
    *
    * @param Question $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Question $entity)
    {
        $form = $this->createForm(new QuestionType(), $entity, array(
            'action' => $this->generateUrl('eph_domande-audit_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Displays a form to create a new Question entity.
     *
     * @Route("/new", name="eph_domande-audit_new", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Question();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Question entity.
     *
     * @Route("/{id}/edit", name="eph_domande-audit_edit", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Question")
     * @Template()
     */
    public function editAction(Question $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Question entity.
     *
     * @Route("/delete/{id}", name="eph_domande-audit_delete", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Question")
     */
    public function deleteAction(Question $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }

        $this->remove($entity);

        return $this->redirect($this->generateUrl('eph_domande-audit'));
    }

    /**
     * Displays a form to edit an existing Question entity.
     *
     * @Route("/group/{id}/{group}", name="eph_domande-audit_group", options={"ACL": {"in_role": "R_EPH"}, "expose": true},defaults={"_format": "json"})
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Question")
     * @ParamConverter("gruppo", class="ClaimsHAuditBundle:Gruppo", options={"id" = "group"})
     */
    public function groupAction(Question $entity, \Claims\HAuditBundle\Entity\Gruppo $gruppo)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }

        $entity->setGruppo($gruppo);
        
        $this->persist($entity);

        return $this->redirect($this->generateUrl('eph_domande-audit'));
    }

    /**
    * Creates a form to edit a Question entity.
    *
    * @param Question $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Question $entity)
    {
        $form = $this->createForm(new QuestionType(), $entity, array(
            'action' => $this->generateUrl('eph_domande-audit_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'btn')));

        return $form;
    }
    /**
     * Edits an existing Question entity.
     *
     * @Route("/{id}", name="eph_domande-audit_update", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("PUT")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Question")
     * @Template("ClaimsHAuditBundle:Question:edit.html.twig")
     */
    public function updateAction(Question $entity)
    {
        $request = $this->getRequest();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('eph_domande-audit'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
}
