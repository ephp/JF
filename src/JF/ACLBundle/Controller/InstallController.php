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
     * @Route("-acl", name="install_acl"), defaults={"_format": "json})
     */
    public function indexAction() {
        $package = 'jf.acl';
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            $this->installPackage($package, 'JF-System ACL', 'JFACLBundle:Install:package.txt.twig');
            
            $licenze[] = $this->newLicenza(
                    $package, 'utenze', 'free', 'Utenza singola',               //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_free.txt.twig',                 //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.utenti'),                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'max' => 1,                                             //  Numero massimo di utenti
                        'rubrica' => false,                                     //  Abilitazione rubrica
                        ),                                          
                    0, null, true, false);                                      //Prezzo
            
            $licenze[] = $this->newLicenza(
                    $package, 'utenze', 'small', 'Piccola Azienda',             //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_small.txt.twig',                //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.utenti'),                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'max' => 5,                                             //  Numero massimo di utenti
                        'rubrica' => true,                                      //  Abilitazione rubrica
                        ),                                          
                    100);                                                       //Prezzo
            
            $licenze[] = $this->newLicenza(
                    $package, 'utenze', 'medium', 'Media Azienda',              //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_medium.txt.twig',               //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.utenti'),                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'max' => 10,                                            //  Numero massimo di utenti
                        'rubrica' => true,                                      //  Abilitazione rubrica
                        ),                                          
                    175);                                                       //Prezzo
            
            $licenze[] = $this->newLicenza(
                    $package, 'utenze', 'big', 'Grande Azienda',                //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_big.txt.twig',                  //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.utenti'),                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'max' => 25,                                            //  Numero massimo di utenti
                        'rubrica' => true,                                      //  Abilitazione rubrica
                        ),                                          
                    250);                                                       //Prezzo
       
            $licenze[] = $this->newLicenza(
                    $package, 'utenze', 'unlimited', 'Unlimited',               //Anagrafica licenza 
                    'JFACLBundle:Install:utenze_unlimited.txt.twig',            //TWIG descrizione
                    null,                                                       //Durata
                    array('R_SUPER', 'R_ADMIN'),                                //Ruoli abilitati
                    array('jf.acl.locked', 'jf.acl.utenti'),                    //Widget abilitati
                    array(                                                      //Parametri di configurazione
                        'max' => 10000,                                         //  Numero massimo di utenti
                        'rubrica' => true,                                      //  Abilitazione rubrica
                        ),                                          
                    500);                                                       //Prezzo

            
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
