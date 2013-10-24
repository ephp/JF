<?php

namespace SLC\HBundle\Controller;

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
     * @Route("-slc-h", name="install_slc_s", defaults={"_format": "json"})
     */
    public function indexAction() {
        $package = 'slc.h';
        $g_pratiche = "analisi";
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            
            $this->installPackage($package, 'SLC-Claims Hospital', 'SLCHBundle:Install:package.txt.twig');

            $this->installGruppo($package, $g_pratiche, 'Analisi sinistri ospedalieri', 'SLCHBundle:Install:analisi.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, $g_pratiche, 'slc', 100, 'Studio Legale Carlesi', //Anagrafica licenza 
                    'SLCHBundle:Install:analisi_slc.txt.twig',                 //TWIG descrizione
                    null,                                                       //Durata
                    array('C_ADMIN', 'C_GESTORE_H', 'C_RECUPERI_H'),            //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'form_cliente' => array(
                            'form' => '\SLC\HBundle\Form\AccountType',      
                            'label' => 'Personalizzazione ospedali',  
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
