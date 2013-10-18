<?php

namespace JF\CalendarBundle\Controller;

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
     * @Route("-calendar", name="install_calendar"), defaults={"_format": "json"})
     */
    public function indexAction() {
        $package = 'jf.cal';
        $g_cal = 'calendario';
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            $this->installPackage($package, 'JF-System Calendar', 'JFCalendarBundle:Install:package.txt.twig');
            $this->installGruppo($package, $g_cal, 'Calendario personale', 'JFCalendarBundle:Install:utenze.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, $g_cal, 'free', 1, 'Calendario base',             //Anagrafica licenza 
                    'JFCalendarBundle:Install:calendario_free.txt.twig',        //TWIG descrizione
                    null,                                                       //Durata
                    array(),                                                    //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'reminder' => false,                                    //  Invio automatico degli appuntamenti giornalieri
                        'ical' => false,                                        //  esportazione ical
                        ),                                          
                    0, null,                                                    //Prezzo-Prezzo scontato
                    true, true);                                                //Autoinstall-Market
            
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
