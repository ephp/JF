<?php

namespace Claims\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Claims\CoreBundle\Entity\StatoPratica;
use Claims\CoreBundle\Form\StatoPraticaType;

/**
 * StatoPratica controller.
 *
 * @Route("/claims/stato-pratica")
 */
class StatoPraticaController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController;
    
    /**
     * Lists all StatoPratica entities.
     *
     * @Route("/", name="claims_stato_pratica")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $entities = $this->findBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $this->getUser()->getCliente()->getId()));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new StatoPratica entity.
     *
     * @Route("/", name="claims_stato_pratica_create")
     * @Method("POST")
     * @Template("ClaimsCoreBundle:StatoPratica:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new StatoPratica();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setCliente($this->getUser()->getCliente());
            $this->persist($entity);

            return $this->redirect($this->generateUrl('claims_stato_pratica'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a StatoPratica entity.
     *
     * @param StatoPratica $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(StatoPratica $entity) {
        $form = $this->createForm(new StatoPraticaType(), $entity, array(
            'action' => $this->generateUrl('claims_stato_pratica_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Displays a form to create a new StatoPratica entity.
     *
     * @Route("/new", name="claims_stato_pratica_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new StatoPratica();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing StatoPratica entity.
     *
     * @Route("/{id}/edit", name="claims_stato_pratica_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $entity = $this->find('ClaimsCoreBundle:StatoPratica', $id);

        if (!$entity || $entity->getCliente()->detId() != $this->getUser()->getCliente()->getId()) {
            throw $this->createNotFoundException('Unable to find StatoPratica entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a StatoPratica entity.
     *
     * @param StatoPratica $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(StatoPratica $entity) {
        $form = $this->createForm(new StatoPraticaType(), $entity, array(
            'action' => $this->generateUrl('claims_stato_pratica_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Edits an existing StatoPratica entity.
     *
     * @Route("/{id}", name="claims_stato_pratica_update")
     * @Method("PUT")
     * @Template("ClaimsCoreBundle:StatoPratica:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $entity = $this->find('ClaimsCoreBundle:StatoPratica', $id);

        if (!$entity || $entity->getCliente()->detId() != $this->getUser()->getCliente()->getId()) {
            throw $this->createNotFoundException('Unable to find StatoPratica entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('claims_stato_pratica'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a StatoPratica entity.
     *
     * @Route("/{id}", name="claims_stato_pratica_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity = $this->find('ClaimsCoreBundle:StatoPratica', $id);

            if (!$entity || $entity->getCliente()->detId() != $this->getUser()->getCliente()->getId()) {
                throw $this->createNotFoundException('Unable to find StatoPratica entity.');
            }

            $this->remove($entity);
        }

        return $this->redirect($this->generateUrl('claims_stato_pratica'));
    }

    /**
     * Creates a form to delete a StatoPratica entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('claims_stato_pratica_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Cancella', 'attr' => array('class' => 'btn')))
                        ->getForm()
        ;
    }

}
