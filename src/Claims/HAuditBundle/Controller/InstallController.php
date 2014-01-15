<?php

namespace Claims\HAuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use JF\CoreBundle\Controller\Traits\InstallController as BaseInstall;

/**
 * @Route("/__install")
 */
class InstallController extends Controller {

    use BaseController,
        BaseInstall;

    /**
     * @Route("-claims-h-audit", name="install_claims_h_audit", defaults={"_format": "json"})
     */
    public function indexAction() {
        $package = 'cl.h.audit';
        $g_pratiche = "audit";
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            
            $this->installPackage($package, 'JF-Claims Hospital Audit', 'ClaimsHAuditBundle:Install:package.txt.twig');

            $this->installGruppo($package, $g_pratiche, 'Gestione audit ospedalieri', 'ClaimsHAuditBundle:Install:audit.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, $g_pratiche, 'base', 10, 'Gestione base',         //Anagrafica licenza 
                    'ClaimsHAuditBundle:Install:audit_base.txt.twig',                //TWIG descrizione
                    365,                                                        //Durata
                    array('C_AUDIT_H', 'C_SUPVIS_H'),                            //Ruoli abilitati
                    array('claims.h-audit.cerca'),                              //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        ),                                          
                    1500);                                                      //Prezzo
            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            $status = 500;
            $message = $e->getMessage();
        }
        
        return array(
            'package' => $package,
            'status' => $status,
            'message' => $message,
            'licenze' => $licenze,
        );
    }

}
