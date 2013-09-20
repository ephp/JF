<?php

namespace JF\AndreaniBundle\Controller;

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
     * @Route("-andreani", name="install_andreani"), defaults={"_format": "json"})
     */
    public function indexAction() {
        $package = 'jf.andr';
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            $this->installPackage($package, 'Widget Andreani', 'JFAndreaniBundle:Install:package.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, 'andreani', 'base', 'Integrazione strumenti Studio Andreani',  
                                                                                //Anagrafica licenza 
                    'JFAndreaniBundle:Install:andreani_base.txt.twig',          //TWIG descrizione
                    365,                                                        //Durata
                    array('ROLE_USER'),                                         //Ruoli abilitati
                    array('jf.andr.widget'),                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                    ),
                    50, 30);                                                    //Prezzo
            
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
