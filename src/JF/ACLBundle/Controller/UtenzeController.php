<?php

namespace JF\ACLBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JF\ACLBundle\Entity\Gestore;
use JF\ACLBundle\Entity\Cliente;
use JF\ACLBundle\Form\GestoreAdminType;
use JF\ACLBundle\Form\ClienteType;
use JF\ACLBundle\Form\AccountType;

/**
 * Gestore controller.
 *
 * @Route("/utenze")
 */
class UtenzeController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \JF\CoreBundle\Controller\Traits\CoreController,
        \Ephp\ACLBundle\Controller\Traits\NotifyController;

    /**
     * Lists all Gestore entities.
     *
     * @Route("/", name="utenze", options={"ACL": {"in_role": "R_SUPER"}})
     * @Template()
     */
    public function indexAction() {
        $cliente = $this->getUser()->getCliente();
        $entities = $this->findBy('JFACLBundle:Gestore', array('cliente' => $cliente->getId()));

        $users = array();
        foreach ($cliente->getUtenze() as $user) {
            $users[] = $user->getId();
        }
        $accessi = $this->findBy($this->container->getParameter('ephp_acl.access_log.class'), array('user' => $users), array('logged_at' => 'DESC'), 10);

        return array(
            'entities' => $entities,
            'accessi' => $accessi,
            'roles' => $this->roles(),
        );
    }

    /**
     * Displays a form to create a new Gestore entity.
     *
     * @Route("/new", name="utenze_new", options={"ACL": {"in_role": "R_SUPER"}})
     * @Template()
     */
    public function newAction() {
        if ($this->getUser()->get('jf.acl-utenze.max') <= $this->countDql('JFACLBundle:Gestore', array('cliente' => $this->getUser()->getCliente()->getId()))) {
            return $this->redirect($this->generateUrl('catalogo'));
        }
        $entity = new Gestore();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new Gestore entity.
     *
     * @Route("/create", name="utenze_create", options={"ACL": {"in_role": "R_SUPER"}})
     * @Method("post")
     * @Template("JFACLBundle:Utenze:new.html.twig")
     */
    public function createAction() {
        $entity = new Gestore();
        $request = $this->getRequest();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $pwd = substr('JFU' . strrev(uniqid()), 0, 8);
            $entity
                    ->setCliente($this->getUser()->getCliente())
                    ->setNome("{$entity->getFirstname()} {$entity->getLastname()}")
                    ->setNickname("{$entity->getFirstname()} {$entity->getLastname()} {$entity->getCliente()->getNome()}")
                    ->setUsername($entity->getEmail())
                    ->setPlainPassword($pwd)
                    ->setEnabled(true)
            ;
            $this->persist($entity);

            $this->notify($entity, $this->getUser()->getCliente()->getNome() . ' ha creato un accesso a JF-SYSTEM per te!', 'JFACLBundle:email:user', array('password' => $pwd));

            return $this->redirect($this->generateUrl('utenze'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a form to create a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Gestore $entity) {
        $form = $this->createForm(new GestoreAdminType(), $entity, array(
            'action' => $this->generateUrl('utenze_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Registra', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Displays a form to edit an existing Gestore entity.
     *
     * @Route("/{slug}/edit", name="utenze_edit", options={"ACL": {"in_role": "R_SUPER"}})
     * @ParamConverter("comment", options={"mapping": {"slug": "slug"}})
     * @Template()
     */
    public function editAction(Gestore $entity) {
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
     * Edits an existing Gestore entity.
     *
     * @Route("/{slug}/update", name="utenze_update", options={"ACL": {"in_role": "R_SUPER"}})
     * @ParamConverter("comment", options={"mapping": {"slug": "slug"}})
     * @Method("put")
     * @Template("JFACLBundle:Utenze:edit.html.twig")
     */
    public function updateAction(Gestore $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($this->getRequest());

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('utenze'));
        }

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
    private function createEditForm(Gestore $entity) {
        $form = $this->createForm(new GestoreAdminType(), $entity, array(
            'action' => $this->generateUrl('utenze_update', array('slug' => $entity->getSlug())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Aggiorna', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Displays a form to edit an existing Gestore entity.
     *
     * @Route("-edit-account", name="utenze_account_edit", options={"ACL": {"in_role": "R_SUPER"}})
     * @ParamConverter("comment", options={"mapping": {"slug": "slug"}})
     * @Template()
     */
    public function editAccountAction() {
        $entity = $this->getUser()->getCliente();
        $editForm = $this->createAccountEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Gestore entity.
     *
     * @Route("-update-account", name="utenze_account_update", options={"ACL": {"in_role": "R_SUPER"}})
     * @Method("put")
     * @Template("JFACLBundle:Utenze:editAccount.html.twig")
     */
    public function updateAccountAction() {
        $entity = $this->getUser()->getCliente();
        /* @var $entity \JF\ACLBundle\Entity\Cliente */
        $dati = $this->getRequest()->get('jf_cliente');
        unset($dati['submit'], $dati['_token']);
        $entity->setDati($dati);
        $this->persist($entity);

        return $this->redirect($this->generateUrl('index'));
    }

    /**
     * Creates a form to edit a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAccountEditForm(Cliente $entity) {
        $form = $this->createForm(new AccountType($this->getUser()->get('form_cliente')), $entity, array(
            'action' => $this->generateUrl('utenze_account_update'),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Aggiorna', 'attr' => array('class' => 'btn')));

        if ($this->getUser()->getCliente()->getDati()) {
            foreach ($this->getUser()->getCliente()->getDati() as $key => $dati) {
                foreach ($dati as $k => $v) {
                    try {
                        $form->get($key)->get($k)->setData($v);
                    } catch (\Exception $e) {}
                }
            }
        }

        return $form;
    }

    /**
     * Displays a form to edit an existing Cliente entity.
     *
     * @Route("-azienda/edit", name="utenze_azienda_edit", options={"ACL": {"in_role": "R_SUPER"}})
     * @Method("GET")
     * @Template("JFACLBundle:Cliente:edit.html.twig")
     */
    public function editAziendaAction() {
        $entity = $this->getUser()->getCliente();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        $editForm = $this->createAziendaEditForm($entity);

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
    private function createAziendaEditForm(Cliente $entity) {
        $form = $this->createForm(new ClienteType(), $entity, array(
            'action' => $this->generateUrl('utenze_azienda_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Aggiorna', 'attr' => array('class' => 'btn')));

        return $form;
    }

    /**
     * Edits an existing Cliente entity.
     *
     * @Route("-azienda/update", name="utenze_azienda_update", options={"ACL": {"in_role": "R_SUPER"}})
     * @Method("PUT")
     * @Template("JFACLBundle:Cliente:edit.html.twig")
     */
    public function updateAziendaAction(Request $request) {
        $entity = $this->getUser()->getCliente();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        $editForm = $this->createAziendaEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->persist($entity);

            return $this->redirect($this->generateUrl('fos_user_profile_show'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Gestore entity.
     *
     * @Route("/{slug}/role/{role}", name="utenze_role", defaults={"_format": "json"}, options={"expose": true, "ACL": {"in_role": "R_SUPER"}})
     * @ParamConverter("comment", options={"mapping": {"slug": "slug"}})
     */
    public function roleAction(Gestore $entity, $role) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gestore entity.');
        }

        $_roles = $this->roles();
        foreach ($_roles as $key => $data) {
            if ($role == $data['abbr']) {
                $role = $key;
            }
        }

        if ($entity->isAccountNonLocked()) {
            $roles = $entity->getRoles();
            if (in_array($role, $roles)) {
                $n = 0;
                if ($role == 'R_SUPER') {
                    foreach ($entity->getCliente()->getUtenze() as $u) {
                        /* @var $u \JF\ACLBundle\Entity\Gestore */
                        $n += $u->hasRole('R_SUPER');
                    }
                }
                if ($role != 'R_SUPER' || $n > 1) {
                    $roles = array_diff($roles, array($role));
                }
            } else {
                $roles[] = $role;
            }
            $entity->setRoles($roles);
            $this->persist($entity);
        }
        return $this->jsonResponse(array('active' => $entity->hasRole($role)));
    }

    /**
     * Deletes a Gestore entity.
     *
     * @Route("/{slug}/lock", name="utenze_lock", defaults={"_format": "json"}, options={"expose": true, "ACL": {"in_role": "R_SUPER"}})
     * @ParamConverter("comment", options={"mapping": {"slug": "slug"}})
     * @Method("post")
     */
    public function lockAction(Gestore $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gestore entity.');
        }
        if ($this->getUser()->getId() != $entity->getId()) {
            $entity->setLocked($entity->isAccountNonLocked());
            $this->persist($entity);
        }

        return $this->jsonResponse(array('locked' => $entity->isLocked()));
    }

    /**
     * Creates a new Gestore entity.
     *
     * @Route("-help", name="help_utenze", options={"expose": true, "ACL": {"in_role": "R_SUPER"}})
     * @Template()
     */
    public function helpIndexAction() {
        return array();
    }

    /**
     * Creates a new Gestore entity.
     *
     * @Route("-help/azienda", name="help_utenze_azienda_edit", options={"expose": true, "ACL": {"in_role": "R_SUPER"}})
     * @Template()
     */
    public function helpAziendaAction() {
        return array();
    }

    /**
     * Creates a new Gestore entity.
     *
     * @Route("/create-eph", name="utenze_eph_create")
     * @Method("post")
     */
    public function createEphAction() {
        if (!$this->findOneBy($this->container->getParameter('ephp_acl.user.class'), array('sigla' => 'EPH'))) {
            $entity = new Gestore();
            $pwd = substr(0, 8, uniqid('JF'));
            $entity
                    ->setSigla('EPH')
                    ->setNome('Ephraim')
                    ->setNickname('Ephraim')
                    ->setEmail('ephraim.pepe@gmail.com')
                    ->setUsername('ephraim')
                    ->setPlainPassword('pci')
                    ->addRole('R_EPH')
                    ->setEnabled(true)
                    ->setPlainPassword($pwd)
            ;
            try {
                $this->persist($entity);
            } catch (\Exception $e) {
                throw $e;
            }
        }
        return $this->redirect($this->generateUrl('utenze'));
    }

}
