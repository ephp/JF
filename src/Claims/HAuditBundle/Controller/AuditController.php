<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Claims\HAuditBundle\Entity\Audit;
use Claims\HAuditBundle\Form\AuditType;

/**
 * Audit controller.
 *
 * @Route("/claims-h-audit")
 */
class AuditController extends Controller
{
    use BaseController;

    /**
     * Lists all Audit entities.
     *
     * @Route("/", name="claims-h-audit")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
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
    public function createAction(Request $request)
    {
        $entity = new Audit();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('claims-h-audit'));

        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Audit entity.
    *
    * @param Audit $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Audit $entity)
    {
        $form = $this->createForm(new AuditType(), $entity, array(
            'action' => $this->generateUrl('claims-h-audit_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Displays a form to create a new Audit entity.
     *
     * @Route("/new", name="claims-h-audit_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Audit();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
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
    public function showAction(Audit $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Audit entity.
     *
     * @Route("/{id}/edit", name="claims-h-audit_edit")
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     * @Template()
     */
    public function editAction(Audit $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Audit entity.
    *
    * @param Audit $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Audit $entity)
    {
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
     * @Template("ClaimsHAuditBundle:Audit:edit.html.twig")
     */
    public function updateAction(Audit $entity)
    {
        $request = $this->getRequest();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('claims-h-audit'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
}
