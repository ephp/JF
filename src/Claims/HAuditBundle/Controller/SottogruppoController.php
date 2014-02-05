<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Claims\HAuditBundle\Entity\Sottogruppo;
use Claims\HAuditBundle\Form\SottogruppoType;

/**
 * Sottogruppo controller.
 *
 * @Route("/eph/sottogruppi-audit")
 */
class SottogruppoController extends Controller
{
    use BaseController;

    /**
     * Lists all Sottogruppo entities.
     *
     * @Route("/", name="eph_sottogruppi-audit")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $entities = $this->findAll('ClaimsHAuditBundle:Sottogruppo');

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Sottogruppo entity.
     *
     * @Route("/", name="eph_sottogruppi-audit_create")
     * @Method("POST")
     * @Template("ClaimsHAuditBundle:Sottogruppo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Sottogruppo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('eph_sottogruppi-audit'));

        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Sottogruppo entity.
    *
    * @param Sottogruppo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Sottogruppo $entity)
    {
        $form = $this->createForm(new SottogruppoType(), $entity, array(
            'action' => $this->generateUrl('eph_sottogruppi-audit_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Displays a form to create a new Sottogruppo entity.
     *
     * @Route("/new", name="eph_sottogruppi-audit_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Sottogruppo();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Sottogruppo entity.
     *
     * @Route("/{id}/edit", name="eph_sottogruppi-audit_edit")
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Sottogruppo")
     * @Template()
     */
    public function editAction(Sottogruppo $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sottogruppo entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Sottogruppo entity.
    *
    * @param Sottogruppo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Sottogruppo $entity)
    {
        $form = $this->createForm(new SottogruppoType(), $entity, array(
            'action' => $this->generateUrl('eph_sottogruppi-audit_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'btn')));

        return $form;
    }
    /**
     * Edits an existing Sottogruppo entity.
     *
     * @Route("/{id}", name="eph_sottogruppi-audit_update")
     * @Method("PUT")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Sottogruppo")
     * @Template("ClaimsHAuditBundle:Sottogruppo:edit.html.twig")
     */
    public function updateAction(Sottogruppo $entity)
    {
        $request = $this->getRequest();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sottogruppo entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('eph_sottogruppi-audit'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
}
