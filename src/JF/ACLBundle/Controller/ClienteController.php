<?php

namespace JF\ACLBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use Ephp\ACLBundle\Controller\Traits\NotifyController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JF\ACLBundle\Entity\Cliente;
use JF\ACLBundle\Entity\Gestore;
use JF\ACLBundle\Entity\Licenza;
use JF\ACLBundle\Form\ClienteType;
use JF\ACLBundle\Form\SuperadminType;

/**
 * Cliente controller.
 *
 * @Route("/eph/clienti")
 */
class ClienteController extends Controller {

    use BaseController,
        NotifyController;

    /**
     * Lists all Cliente entities.
     *
     * @Route("/", name="eph_clienti", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JFACLBundle:Cliente')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Cliente entity.
     *
     * @Route("/", name="eph_clienti_create", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("POST")
     * @Template("JFACLBundle:Cliente:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Cliente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setPresentatore($this->getUser());
            $this->persist($entity);
            
            foreach($this->findBy('JFCoreBundle:Licenza', array('autoinstall' => true)) as $licenza) {
                $_licenza = new Licenza();
                $_licenza->setLicenza($licenza);
                $_licenza->setCliente($entity);
                $_licenza->setPagamento(new \DateTime());
                $this->persist($_licenza);
            }

            return $this->redirect($this->generateUrl('eph_clienti_super_new', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Cliente entity.
     *
     * @Route("/new", name="eph_clienti_new", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Cliente();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Cliente $entity) {
        $form = $this->createForm(new ClienteType(), $entity, array(
            'action' => $this->generateUrl('eph_clienti_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Procedi', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Creates a new Cliente entity.
     *
     * @Route("/super", name="eph_clienti_super_create", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("POST")
     * @Template("JFACLBundle:Cliente:newSuper.html.twig")
     */
    public function createSuperAction(Request $request) {
        $entity = new Gestore();
        $form = $this->createCreateSuperForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $this->getEm()->beginTransaction();
                $pwd = substr('JFS' . strrev(uniqid()), 0, 8);
                $entity
                        ->setNome("{$entity->getFirstname()} {$entity->getLastname()}")
                        ->setNickname("{$entity->getFirstname()} {$entity->getLastname()} {$entity->getCliente()->getNome()}")
                        ->setUsername($entity->getEmail())
                        ->setPlainPassword($pwd)
                        ->addRole('R_SUPER')
                        ->setEnabled(true)
                ;

                $this->persist($entity);
                $entity->getCliente()->setReferente($entity);
                $this->persist($entity->getCliente());
                
                $this->notify($entity, 'Il tuo account su JF-SYSTEM è stato abilitato', 'JFACLBundle:email:superuser', array('password' => $pwd));

                $this->getEm()->commit();
            } catch (\Exception $e) {
                $this->getEm()->rollback();
                throw $e;
            }
            return $this->redirect($this->generateUrl('eph_clienti'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Cliente entity.
     *
     * @Route("/new/super/{id}", name="eph_clienti_super_new", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("GET")
     * @ParamConverter("id", class="JFACLBundle:Cliente")
     * @Template()
     */
    public function newSuperAction(Cliente $cliente) {
        $entity = new Gestore();
        $entity->setCliente($cliente);

        $form = $this->createCreateSuperForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateSuperForm(Gestore $entity) {
        $form = $this->createForm(new SuperadminType(), $entity, array(
            'action' => $this->generateUrl('eph_clienti_super_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Attiva', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Finds and displays a Cliente entity.
     *
     * @Route("/{id}", name="eph_clienti_show", options={"ACL": {"in_role": "R_EPH"}})
     * @ParamConverter("id", class="JFACLBundle:Cliente")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Cliente $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        $users = array();
        foreach ($entity->getUtenze() as $user) {
            $users[] = $user->getId();
        }
        $accessi = $this->findBy($this->container->getParameter('ephp_acl.access_log.class'), array('user' => $users), array('logged_at' => 'DESC'), 10);

        return array(
            'entity' => $entity,
            'accessi' => $accessi,
        );
    }

    /**
     * Displays a form to edit an existing Cliente entity.
     *
     * @Route("/{id}/edit", name="eph_clienti_edit", options={"ACL": {"in_role": "R_EPH"}})
     * @ParamConverter("id", class="JFACLBundle:Cliente")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Cliente $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Cliente $entity) {
        $form = $this->createForm(new ClienteType(), $entity, array(
            'action' => $this->generateUrl('eph_clienti_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Aggiorna', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Edits an existing Cliente entity.
     *
     * @Route("/{id}", name="eph_clienti_update", options={"ACL": {"in_role": "R_EPH"}})
     * @Method("PUT")
     * @Template("JFACLBundle:Cliente:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JFACLBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('eph_clienti_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Cliente entity.
     *
     * @Route("-sospendi/{id}", name="eph_clienti_sospendi", options={"ACL": {"in_role": "R_EPH"}})
     * @ParamConverter("id", class="JFACLBundle:Cliente")
     */
    public function deleteAction(Cliente $entity) {
        $entity->setBloccato(!$entity->getBloccato());
        $this->persist($entity);
        return $this->redirect($this->generateUrl('eph_clienti'));
    }

    /**
     * Creates a form to delete a Cliente entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('eph_clienti_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    /**
     * Help per il form clienti
     *
     * @Route("-help-form", name="help_eph_clienti_new", options={"expose"=true, "ACL": {"in_role": "R_EPH"}})
     * @Route("-help-form", name="help_eph_clienti_edit", options={"expose"=true, "ACL": {"in_role": "R_EPH"}})
     * @Route("-help-form", name="help_eph_clienti_super_new", options={"expose"=true, "ACL": {"in_role": "R_EPH"}})
     * @Template()
     */
    public function helpFormAction() {
        return array();
    }

}
