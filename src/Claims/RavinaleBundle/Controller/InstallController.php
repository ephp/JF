<?php

namespace Claims\RavinaleBundle\Controller;

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
     * @Route("-claims-ravinale", name="install_claims_ravinale", defaults={"_format": "json"})
     */
    public function indexAction() {
        $package = 'cl.h.ravinale';
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            
            $this->installPackage($package, 'JF-Claims Ravinale', 'ClaimsRavinaleBundle:Install:package.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, 'ravinale', 'slc', 'Importazione Ravinale',     //Anagrafica licenza 
                    'ClaimsRavinaleBundle:Install:h.ravinale_slc.txt.twig',     //TWIG descrizione
                    null,                                                       //Durata
                    array('C_ADMIN'),                                           //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'form_cliente' => '\Claims\RavinaleBundle\Form\AccountType',
                                                                                //  Form per opzioni personalizzate
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
