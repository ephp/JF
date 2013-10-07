<?php

namespace JF\ACLBundle\Controller;

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
     * @Route("-acl", name="install_acl"), defaults={"_format": "json"})
     */
    public function indexAction() {
        $package = 'jf.acl';
        $g_utenze = 'utenze';
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            $this->installPackage($package, 'JF-System ACL', 'JFACLBundle:Install:package.txt.twig');
            $this->installGruppo($package, $g_utenze, 'Gestione utenze', 'JFACLBundle:Install:utenze.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, $g_utenze, 'free', 1, 'Utenza singola',           //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_free.txt.twig',                 //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.licenze'),                   //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'max' => 1,                                             //  Numero massimo di utenti
                        'rubrica' => false,                                     //  Abilitazione rubrica
                        ),                                          
                    0, null,                                                    //Prezzo-Prezzo scontato
                    true, true);                                                //Autoinstall-Market
            
            $licenze[] = $this->newLicenza(
                    $package, $g_utenze, 'small', 5, 'Piccola Azienda',         //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_small.txt.twig',                //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.utenti','jf.acl.licenze'),   //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'max' => 5,                                             //  Numero massimo di utenti
                        'rubrica' => true,                                      //  Abilitazione rubrica
                        ),                                          
                    100, null,                                                  //Prezzo-Prezzo scontato
                    false, true);                                               //Autoinstall-Market
            
            $licenze[] = $this->newLicenza(
                    $package, $g_utenze, 'medium', 10, 'Media Azienda',         //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_medium.txt.twig',               //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.utenti','jf.acl.licenze'),   //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'max' => 10,                                            //  Numero massimo di utenti
                        'rubrica' => true,                                      //  Abilitazione rubrica
                        ),                                          
                    175, null,                                                  //Prezzo-Prezzo scontato
                    false, true);                                               //Autoinstall-Market
            
            $licenze[] = $this->newLicenza(
                    $package, $g_utenze, 'big', 25, 'Grande Azienda',           //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_big.txt.twig',                  //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.utenti','jf.acl.licenze'),   //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'max' => 25,                                            //  Numero massimo di utenti
                        'rubrica' => true,                                      //  Abilitazione rubrica
                        ),                                          
                    250, null,                                                  //Prezzo-Prezzo scontato
                    false, true);                                               //Autoinstall-Market
       
            $licenze[] = $this->newLicenza(
                    $package, $g_utenze, 'unlimited', 50, 'Unlimited',          //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_unlimited.txt.twig',            //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.utenti','jf.acl.licenze'),   //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'max' => 10000,                                         //  Numero massimo di utenti
                        'rubrica' => true,                                      //  Abilitazione rubrica
                        ),                                          
                    500, null,                                                  //Prezzo-Prezzo scontato
                    false, true);                                               //Autoinstall-Market
            
            $licenze[] = $this->newLicenza(
                    $package, $g_utenze, 'slc', 100, 'Studio Legale Carlesi',   //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_slc.txt.twig',                  //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.utenti','jf.acl.licenze'),   //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'on' => true,                                           //  Abilitazione del package
                        'max' => 10000,                                         //  Numero massimo di utenti
                        'rubrica' => true,                                      //  Abilitazione rubrica
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
