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
        $g_import = 'import';
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            
            $this->installPackage($package, 'JF-Claims Ravinale', 'ClaimsRavinaleBundle:Install:package.txt.twig');
            
            $this->installGruppo($package, $g_import, 'Importazione Ravinale', 'ClaimsRavinaleBundle:Install:import.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, $g_import, 'manual', 10, 'Importazione Manuale',  //Anagrafica licenza 
                    'ClaimsRavinaleBundle:Install:import_manual.txt.twig',      //TWIG descrizione
                    null,                                                       //Durata
                    array('C_ADMIN'),                                           //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        ),                                          
                    500, null,                                                  //Prezzo-Prezzo scontato
                    false, true);                                               //Autoinstall-Market
            $licenze[] = $this->newLicenza(
                    $package, $g_import, 'auto', 20, 'Importazione Automatica', //Anagrafica licenza 
                    'ClaimsRavinaleBundle:Install:import_auto.txt.twig',        //TWIG descrizione
                    null,                                                       //Durata
                    array('C_ADMIN'),                                           //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'form_cliente' => array(
                            'form' => '\Claims\RavinaleBundle\Form\AccountType',
                            'label' => 'Dati Ravinale',
                        ),                                          
                    ),                                          
                    1500, null,                                                 //Prezzo-Prezzo scontato
                    false, true);                                               //Autoinstall-Market
            $licenze[] = $this->newLicenza(
                    $package, $g_import, 'slc', 100, 'Importazione Ravinale',   //Anagrafica licenza 
                    'ClaimsRavinaleBundle:Install:import_slc.txt.twig',         //TWIG descrizione
                    null,                                                       //Durata
                    array('C_ADMIN'),                                           //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'form_cliente' => array(
                            'form' => '\Claims\RavinaleBundle\Form\AccountType',
                            'label' => 'Dati Ravinale',
                        ),                                          
                    ),                                          
                    0, null,                                                    //Prezzo-Prezzo scontato
                    false, false);                                              //Autoinstall-Market
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
