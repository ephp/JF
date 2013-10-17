<?php

namespace Claims\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Claims\HBundle\Entity\Pratica;
use Claims\HBundle\Form\RicercaType;

/**
 * @Route("/render-claims-hospital")
 * @Template()
 */
class RenderController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * @Route("/priorita", name="render_claims_hospital_priorita")
     * @Template()
     */
    public function cambiaPrioritaAction() {
        $priorita = $this->findBy('ClaimsCoreBundle:Priorita', array('show' => true));
        return array(
            'priorita' => $priorita,
        );
    }

    /**
     * @Route("/dasc", name="render_claims_hospital_dasc")
     * @Template()
     */
    public function cambiaDascAction() {
        return array(
        );
    }

    /**
     * @Route("/gestore", name="render_claims_hospital_gestori")
     * @Template()
     */
    public function cambiaGestoreAction() {
        $gestori = $this->findBy('JFACLBundle:Gestore', array('cliente' => $this->getUser()->getCliente()->getId()));
        return array(
            'gestori' => $gestori,
        );
    }

    /**
     * @Route("/note", name="render_claims_hospital_note")
     * @Template()
     */
    public function cambiaNoteAction() {
        return array(
        );
    }

    /**
     * @Route("/stato", name="render_claims_hospital_stato")
     * @Template()
     */
    public function cambiaStatoAction() {
        $stati = $this->findBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $this->getUser()->getCliente()->getId()));
        return array(
            'stati' => $stati,
        );
    }
    
    /**
     * @Route("/dettagli", name="render_claims_hospital_dettagli")
     * @Template()
     */
    public function dettagliAction() {
        return array(
        );
    }
    
    /**
     * @Route("/ricerca/{mode}", name="render_claims_hospital_ricerca")
     * @Template()
     */
    public function ricercaAction($mode) {
        $entity = new Pratica();
        $params = $this->getParam('ricerca');
        if($params) {
            foreach ($params as $param => $value) {
                if(!in_array($param, array('submit', '_token')) && $value) {
                    $fx = \Doctrine\Common\Util\Inflector::camelize('set_'.$param);
                    switch ($param) {
                        case 'ospedale':
                            $value = $this->find('ClaimsHBundle:Ospedale', $value);
                            break;
                        case 'gestore':
                            $value = $this->find('JFACLBundle:Gestore', $value);
                            break;
                        case 'statoPratica':
                            $value = $this->find('ClaimsCoreBundle:StatoPratica', $value);
                            break;
                        case 'priorita':
                            $value = $this->find('ClaimsCoreBundle:Priorita', $value);
                            break;
                    }
                    $entity->$fx($value);
                }
            }
        }
        $form = $this->createCercaForm($entity, $mode);
        $form->handleRequest($this->getRequest());

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
    private function createCercaForm(Pratica $entity, $mode) {
        $form = $this->createForm(new RicercaType($this->getUser()->getCliente()), $entity, array(
            'action' => $this->generateUrl('claims_hospital_'.$mode),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => ' Cerca', 'attr' => array('class' => 'btn ico-search')));

        return $form;
    }
    
    /**
     * @Route("/evento/{id}", name="render_claims_hospital_evento")
     * @Template()
     */
    public function nuovoEventoAction($id) {
        return array(
            'entity' => $this->find('ClaimsHBundle:Pratica', $id),
        );
    }
    
    /**
     * @Route("/link/{id}", name="render_claims_hospital_link")
     * @Template()
     */
    public function nuovoLinkAction($id) {
        return array(
            'entity' => $this->find('ClaimsHBundle:Pratica', $id),
        );
    }

    
}