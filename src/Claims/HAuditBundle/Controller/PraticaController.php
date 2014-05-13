<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Claims\HAuditBundle\Entity\Pratica;
use Claims\HAuditBundle\Form\PraticaType;

/**
 * Pratica controller.
 *
 * @Route("/claims-h-audit/soft/pratica")
 */
class PraticaController extends Controller
{
    use BaseController;

    /**
     * Displays a form to edit an existing Pratica entity.
     *
     * @Route("/{slug}/edit", name="claims-h-audit_soft_pratica_edit")
     * @Method("GET")
     * @ParamConverter("comment", options={"mapping": {"slug": "slug"}})     
     * @Template()
     */
    public function editAction(Pratica $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Pratica entity.
    *
    * @param Pratica $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pratica $entity)
    {
        $form = $this->createForm(new PraticaType(), $entity, array(
            'action' => $this->generateUrl('claims-h-audit_soft_pratica_update', array('slug' => $entity->getSlug())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'btn')));

        return $form;
    }
    /**
     * Edits an existing Pratica entity.
     *
     * @Route("/{slug}", name="claims-h-audit_soft_pratica_update")
     * @Method("PUT")
     * @ParamConverter("comment", options={"mapping": {"slug": "slug"}})     
     * @Template("ClaimsHAuditBundle:Pratica:edit.html.twig")
     */
    public function updateAction(Pratica $entity)
    {
        $request = $this->getRequest();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pratica entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('claims-h-audit_show-pratica', array('slug' => $entity->getSlug(), 'id' => $entity->getAudit()->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
}
