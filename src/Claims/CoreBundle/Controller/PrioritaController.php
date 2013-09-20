<?php

namespace Claims\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Claims\CoreBundle\Entity\Priorita;
use Claims\CoreBundle\Form\PrioritaType;

/**
 * Priorita controller.
 *
 * @Route("/eph/priorita")
 */
class PrioritaController extends Controller
{

    /**
     * Lists all Priorita entities.
     *
     * @Route("/", name="eph_priorita")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ClaimsCoreBundle:Priorita')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Priorita entity.
     *
     * @Route("/", name="eph_priorita_create")
     * @Method("POST")
     * @Template("ClaimsCoreBundle:Priorita:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Priorita();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('eph_priorita'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Priorita entity.
    *
    * @param Priorita $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Priorita $entity)
    {
        $form = $this->createForm(new PrioritaType(), $entity, array(
            'action' => $this->generateUrl('eph_priorita_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crea', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Displays a form to create a new Priorita entity.
     *
     * @Route("/new", name="eph_priorita_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Priorita();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Priorita entity.
     *
     * @Route("/{id}/edit", name="eph_priorita_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ClaimsCoreBundle:Priorita')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Priorita entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Priorita entity.
    *
    * @param Priorita $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Priorita $entity)
    {
        $form = $this->createForm(new PrioritaType(), $entity, array(
            'action' => $this->generateUrl('eph_priorita_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Aggiorna', 'attr' => array('class' => 'btn')));

        return $form;
    }
    /**
     * Edits an existing Priorita entity.
     *
     * @Route("/{id}", name="eph_priorita_update")
     * @Method("PUT")
     * @Template("ClaimsCoreBundle:Priorita:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ClaimsCoreBundle:Priorita')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Priorita entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('eph_priorita'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
}
