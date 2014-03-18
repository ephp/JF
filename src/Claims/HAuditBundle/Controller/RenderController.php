<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use Ephp\UtilityBundle\PhpExcel\SpreadsheetExcelReader;
use Ephp\UtilityBundle\Utility\String;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Claims\HAuditBundle\Entity\Pratica;
use Claims\HAuditBundle\Entity\Audit;
use Claims\HAuditBundle\Entity\Question;
use Claims\HAuditBundle\Form\AuditType;
use Claims\HAuditBundle\Form\QuestionType;

/**
 * Audit controller.
 *
 * @Route("/render-claims-h-audit")
 */
class RenderController extends Controller {

    use BaseController;
    
    /**
     * @Route("/priorita", name="render_claims_hospital_audit_priorita")
     * @Template()
     */
    public function cambiaPrioritaAction() {
        $priorita = $this->findBy('ClaimsCoreBundle:Priorita', array('area' => 'audit', 'show' => true));
        return array(
            'priorita' => $priorita,
        );
    }
    
    /**
     * @Route("/priorita", name="render_claims_hospital_audit_semaforo")
     * @Template()
     */
    public function cambiaSemaforoAction() {
        $priorita = $this->findBy('ClaimsCoreBundle:Priorita', array('area' => 'audit-v', 'show' => true));
        return array(
            'priorita' => $priorita,
        );
    }

    /**
     * @Route("/gestore", name="render_claims_hospital_audit_gestori")
     * @Template()
     */
    public function cambiaGestoreAction() {
        $gestori = $this->getRepository('JFACLBundle:Gestore')
                ->createQueryBuilder('u')
                ->where('u.roles LIKE :audit')
                ->setParameter('audit', '%"C_AUDIT_H"%')
                ->andWhere('u.sigla != :eph')
                ->setParameter('eph', 'EPH')
                ->getQuery()
                ->execute();
        return array(
            'gestori' => $gestori,
        );
    }
    
    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-ricerca/{id}", name="claims-h-audit_ricerca")
     * @Method("GET")
     * @ParamConverter("id", class="ClaimsHAuditBundle:Audit")
     * @Template()
     */
    public function ricercaAction(Audit $entity) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Audit entity.');
        }
        return array(
            'entity' => $entity,
            'ricerca' => $this->getParam('ricerca'),
            'route' => $this->getParam('route'),
        );
    }

    /**
     * Finds and displays a Audit entity.
     *
     * @Route("-ricerca-full", name="claims-h-audit_ricerca")
     * @Method("GET")
     * @Template("ClaimsHAuditBundle:Render:ricerca.html.twig")
     */
    public function ricercaFullAction() {
        return array(
            'entity' => null,
            'ricerca' => $this->getParam('ricerca'),
            'route' => $this->getParam('route'),
        );
    }
}
