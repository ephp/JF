<?php

namespace Claims\HBundle\Controller;

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
     * @Route("-claims-h", name="install_claims_s", defaults={"_format": "json"})
     */
    public function indexAction() {
        $package = 'cl.h';
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            
            $this->installPackage($package, 'JF-Claims Hospital', 'ClaimsHBundle:Install:package.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, 'hospital', 'slc', 'Studio Legale Carlesi',       //Anagrafica licenza 
                    'ClaimsHBundle:Install:hospital_slc.txt.twig',              //TWIG descrizione
                    null,                                                       //Durata
                    array('C_ADMIN', 'C_GESTORE_H'),                            //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        ),                                          
                    0, null, false, true);                                      //Prezzo
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
