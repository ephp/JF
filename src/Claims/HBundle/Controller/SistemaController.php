<?php

namespace Claims\HBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Claims\HBundle\Entity\Sistema;
use Claims\HBundle\Form\SistemaType;

/**
 * Sistema controller.
 *
 * @Route("/eph/claims/h/sistemi")
 */
class SistemaController extends Controller {

    use BaseController;

    /**
     * Lists all Sistema entities.
     *
     * @Route("/", name="eph_claims_h_sistemi")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $entities = $this->findAll('ClaimsHBundle:Sistema');

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Sistema entity.
     *
     * @Route("/", name="eph_claims_h_sistemi_create")
     * @Method("POST")
     * @Template("ClaimsHBundle:Sistema:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Sistema();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('eph_claims_h_sistemi'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Sistema entity.
     *
     * @param Sistema $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Sistema $entity) {
        $form = $this->createForm(new SistemaType(), $entity, array(
            'action' => $this->generateUrl('eph_claims_h_sistemi_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Displays a form to create a new Sistema entity.
     *
     * @Route("/new", name="eph_claims_h_sistemi_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Sistema();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Sistema entity.
     *
     * @Route("/{id}/edit", name="eph_claims_h_sistemi_edit", options={"ACL": {"in_role": "R_EPH"}})
     * @ParamConverter("id", class="ClaimsHBundle:Sistema")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Sistema $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sistema entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Sistema entity.
     *
     * @param Sistema $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Sistema $entity) {
        $form = $this->createForm(new SistemaType(), $entity, array(
            'action' => $this->generateUrl('eph_claims_h_sistemi_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Edits an existing Sistema entity.
     *
     * @Route("/{id}", name="eph_claims_h_sistemi_update")
     * @Method("PUT")
     * @ParamConverter("id", class="ClaimsHBundle:Sistema")
     * @Template("ClaimsHBundle:Sistema:edit.html.twig")
     */
    public function updateAction(Sistema $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sistema entity.');
        }
        $editForm = $this->createEditForm($entity);
        $request = $this->getRequest();
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('eph_claims_h_sistemi'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

}
