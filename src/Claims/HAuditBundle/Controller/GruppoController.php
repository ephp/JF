<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Claims\HAuditBundle\Entity\Gruppo;
use Claims\HAuditBundle\Form\GruppoType;

/**
 * Gruppo controller.
 *
 * @Route("/eph/gurppi-audit")
 */
class GruppoController extends Controller
{
    use BaseController;

    /**
     * Lists all Gruppo entities.
     *
     * @Route("/", name="eph_gurppi-audit", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $entities = $this->findAll('ClaimsHAuditBundle:Gruppo');

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Gruppo entity.
     *
     * @Route("/", name="eph_gurppi-audit_create", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("POST")
     * @Template("ClaimsHAuditBundle:Gruppo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Gruppo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('eph_gurppi-audit'));

        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Gruppo entity.
    *
    * @param Gruppo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Gruppo $entity)
    {
        $form = $this->createForm(new GruppoType(), $entity, array(
            'action' => $this->generateUrl('eph_gurppi-audit_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Displays a form to create a new Gruppo entity.
     *
     * @Route("/new", name="eph_gurppi-audit_new", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Gruppo();
        $entity->setShow(true);
        $entity->setOrdine($this->countDql('ClaimsHAuditBundle:Gruppo') + 1);
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Gruppo entity.
     *
     * @Route("/{id}/edit", name="eph_gurppi-audit_edit", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Gruppo")
     * @Template()
     */
    public function editAction(Gruppo $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gruppo entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Gruppo entity.
    *
    * @param Gruppo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Gruppo $entity)
    {
        $form = $this->createForm(new GruppoType(), $entity, array(
            'action' => $this->generateUrl('eph_gurppi-audit_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'btn')));

        return $form;
    }
    /**
     * Edits an existing Gruppo entity.
     *
     * @Route("/{id}", name="eph_gurppi-audit_update")
     * @Method("PUT")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Gruppo", options={"ACL": {"in_role": "R_EPH"}})
     * @Template("ClaimsHAuditBundle:Gruppo:edit.html.twig")
     */
    public function updateAction(Gruppo $entity)
    {
        $request = $this->getRequest();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gruppo entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('eph_gurppi-audit'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
}
