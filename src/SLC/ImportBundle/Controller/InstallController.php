<?php

namespace SLC\ImportBundle\Controller;

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
     * @Route("-slc-import", name="install_slc_import", defaults={"_format": "json"})
     */
    public function indexAction() {
        $package = 'slc.h.import';
        $g_import = "import";
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            
            $this->installPackage($package, 'Import da JF-Claims', 'SLCImportBundle:Install:package.txt.twig');

            $this->installGruppo($package, $g_import, 'Import da JF-Claims', 'SLCImportBundle:Install:import.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, $g_import, 'slc', 100, 'Import da JF-Claims',     //Anagrafica licenza 
                    'SLCImportBundle:Install:import_slc.txt.twig',              //TWIG descrizione
                    null,                                                       //Durata
                    array('C_ADMIN'),                                           //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'label_cliente' => 'Dati JF-Claims',
                        'form_cliente' => '\SLC\ImportBundle\Form\AccountType',
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
