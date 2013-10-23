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
        $g_pratiche = "pratiche";
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            
            $this->installPackage($package, 'JF-Claims Hospital', 'ClaimsHBundle:Install:package.txt.twig');

            $this->installGruppo($package, $g_pratiche, 'Gestione sinistri ospedalieri', 'ClaimsHBundle:Install:pratiche.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, $g_pratiche, 'base', 10, 'Gestione base',         //Anagrafica licenza 
                    'ClaimsHBundle:Install:pratiche_base.txt.twig',             //TWIG descrizione
                    365,                                                        //Durata
                    array('C_ADMIN', 'C_GESTORE_H', 'C_RECUPERI_H'),            //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'daily_email' => false,                                 //  Invio email giornaliere
                        '30day_verify' => false,                                //  Verifica a 30 giorni
                        'status_view' => false,                                 //  Visione per stati operativi
                        ),                                          
                    1500);                                                      //Prezzo
            $licenze[] = $this->newLicenza(
                    $package, $g_pratiche, 'cal', 20, 'Con gestione attività giornaliere',
                                                                                //Anagrafica licenza 
                    'ClaimsHBundle:Install:pratiche_cal.txt.twig',              //TWIG descrizione
                    365,                                                        //Durata
                    array('C_ADMIN', 'C_GESTORE_H', 'C_RECUPERI_H', 'C_SUPVIS_H'),
                                                                                //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'daily_email' => true,                                  //  Invio email giornaliere
                        '30day_verify' => false,                                //  Verifica a 30 giorni
                        'status_view' => false,                                 //  Visione per stati operativi
                        'label_cliente' => 'Dati operazioni da email',  
                        'form_cliente' => '\Claims\HBundle\Form\AccountType',   
                        ),                                          
                    2500);                                                      //Prezzo
            $licenze[] = $this->newLicenza(
                    $package, $g_pratiche, 'full', 30, 'Completa',              //Anagrafica licenza 
                    'ClaimsHBundle:Install:pratiche_full.txt.twig',             //TWIG descrizione
                    365,                                                        //Durata
                    array('C_ADMIN', 'C_GESTORE_H', 'C_RECUPERI_H', 'C_SUPVIS_H'),
                                                                                //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'daily_email' => true,                                  //  Invio email giornaliere
                        '30day_verify' => true,                                 //  Verifica a 30 giorni
                        'status_view' => true,                                  //  Visione per stati operativi
                        'label_cliente' => 'Dati operazioni da email',  
                        'form_cliente' => '\Claims\HBundle\Form\AccountType',   
                        ),                                          
                    3000);                                                      //Prezzo
            $licenze[] = $this->newLicenza(
                    $package, $g_pratiche, 'trial', 5, 'Completa - prova 30gg', //Anagrafica licenza 
                    'ClaimsHBundle:Install:pratiche_trial.txt.twig',            //TWIG descrizione
                    30,                                                         //Durata
                    array('C_ADMIN', 'C_GESTORE_H', 'C_RECUPERI_H', 'C_SUPVIS_H'),
                                                                                //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'daily_email' => true,                                  //  Invio email giornaliere
                        '30day_verify' => false,                                //  Verifica a 30 giorni
                        'status_view' => true,                                  //  Visione per stati operativi
                        'label_cliente' => 'Dati operazioni da email',  
                        'form_cliente' => '\Claims\HBundle\Form\AccountType',   
                        ),                                          
                    0, null,                                                    //Prezzo-Prezzo scontato
                    false, false);                                              //Autoinstall-Market
            $licenze[] = $this->newLicenza(
                    $package, $g_pratiche, 'slc', 100, 'Studio Legale Carlesi', //Anagrafica licenza 
                    'ClaimsHBundle:Install:pratiche_slc.txt.twig',              //TWIG descrizione
                    null,                                                       //Durata
                    array('C_ADMIN', 'C_GESTORE_H', 'C_RECUPERI_H', 'C_SUPVIS_H'),
                                                                                //Ruoli abilitati
                    array(),                                                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'daily_email' => true,                                  //  Invio email giornaliere
                        '30day_verify' => true,                                 //  Verifica a 30 giorni
                        'status_view' => true,                                  //  Visione per stati operativi
                        'label_cliente' => 'Dati operazioni da email',  
                        'form_cliente' => '\Claims\HBundle\Form\AccountSLCType',   
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
