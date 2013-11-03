<?php

namespace SLC\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/slc/claims-hospital/analisi")
 */
class TabelloneController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\UtilityBundle\Controller\Traits\PaginatorController,
        Traits\TabelloneController;

    /**
     * @Route("/",                name="slc_hospital",            defaults={"tab": "default"},    options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("/analisi-np",      name="slc_hospital_np",         defaults={"tab": "np"},         options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("/analisi-np-sg",   name="slc_hospital_np_sg",      defaults={"tab": "npsg"},       options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("/analisi-np-cg",   name="slc_hospital_np_cg",      defaults={"tab": "npcg"},       options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("/analisi-riserve", name="slc_hospital_riserve",    defaults={"tab": "riserve"},    options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("/bookeeping",      name="slc_hospital_bookeeping", defaults={"tab": "bookeeping"}, options={"ACL": {"in_role": {"R_SUPER"}}})
     * @Template()
     */
    public function indexAction($tab) {
        $filtri = $this->buildFiltriSlc($tab);
        $pagination = $this->createPagination($this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri), 50);
        return array(
            'links' => $this->buildLinksSlc(),
            'pagination' => $pagination,
            'tabopen' => $tab,
        );
    }

    /**
     * @Route("-stampa",                 name="slc_hospital_stampa",            defaults={"tab": "default"},    options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-analisi-np",      name="slc_hospital_np_stampa",         defaults={"tab": "np"},         options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-analisi-np-sg",   name="slc_hospital_np_sg_stampa",      defaults={"tab": "npsg"},         options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-analisi-np-cg",   name="slc_hospital_np_cg_stampa",      defaults={"tab": "npcg"},         options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-analisi-riserve", name="slc_hospital_riserve_stampa",    defaults={"tab": "riserve"},    options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Route("-stampa-bookeeping",      name="slc_hospital_bookeeping_stampa", defaults={"tab": "bookeeping"}, options={"ACL": {"in_role": {"R_SUPER"}}})
     * @Template()
     */
    public function stampaAction($tab) {
        $filtri = $this->buildFiltriSlc($tab);
        $entities = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();
        return array(
            'entities' => $entities,
            'show_gestore' => true,
            'tabopen' => $tab,
        );
    }

}
