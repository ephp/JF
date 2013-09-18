<?php

namespace JF\CoreBundle\Controller\Traits;

trait InstallController {

    protected function installPackage($sigla, $nome, $descrizione) {
        $package = $this->findPackage($sigla);
        if (!$package) {
            $package = new \JF\CoreBundle\Entity\Package();
            $package->setSigla($sigla)
                    ->setNome($nome)
                    ->setDescrizione($this->renderView($descrizione));
            $this->persist($package);
        } else {
            $package->setNome($nome)
                    ->setDescrizione($this->renderView($descrizione));
            $this->persist($package);
        }
        
    }

    protected function findPackage($sigla) {
        return $this->findOneBy('JFCoreBundle:Package', array('sigla' => $sigla));
    }

    protected function newLicenza($package, $gruppo, $sigla, $nome, $descrizione, $durata, $roles, $widgets, $params, $prezzo, $sconto = null, $autoinstall = false, $market = true) {
        $package = $this->findPackage($package);
        $licenza = $this->findLicenza($package, $gruppo, $sigla);
        if (!$licenza) {
            $licenza = new \JF\CoreBundle\Entity\Licenza();
            $licenza->setPackage($package)
                    ->setGruppo($gruppo)
                    ->setSigla($sigla)
                    ->setNome($nome)
                    ->setDescrizione($this->renderView($descrizione))
                    ->setDurata($durata)
                    ->setRoles($roles)
                    ->setWidgets($widgets)
                    ->setParams($params)
                    ->setPrezzo($prezzo)
                    ->setSconto($sconto ?: $prezzo)
                    ->setAutoinstall($autoinstall)
                    ->setMarket($market)
                    ->setStato($sconto ? \JF\CoreBundle\Entity\Licenza::$S_LIM : \JF\CoreBundle\Entity\Licenza::$S_NEW)
                    ;
            if(!$market) {
                $licenza->setStato(\JF\CoreBundle\Entity\Licenza::$S_HID);
            }
            $this->persist($licenza);
        } else {
            $stato = \JF\CoreBundle\Entity\Licenza::$S_NOP;
            $descrizione = $this->renderView($descrizione);
            if($licenza->getDescrizione() != $descrizione) {
                $licenza->setDescrizione($descrizione);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if($licenza->getDurata() != $durata) {
                $licenza->setDurata($durata);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if($licenza->getAutoinstall() != $autoinstall) {
                $licenza->setAutoinstall($autoinstall);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if(count(array_diff($licenza->getRoles(), $roles)) != 0) {
                $licenza->setRoles($roles);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if(count(array_diff($licenza->getParams(), $params)) != 0) {
                $licenza->setParams($params);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if(count(array_diff($licenza->getWidgets(), $widgets)) != 0) {
                $licenza->setWidgets($widgets);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if($licenza->getPrezzo() != $prezzo) {
                $stato = $licenza->getPrezzo() > $prezzo ? \JF\CoreBundle\Entity\Licenza::$S_DOW : \JF\CoreBundle\Entity\Licenza::$S_UPD;
                $licenza->setPrezzo($prezzo);
                $licenza->setSconto($prezzo);
            }
            if($sconto) {
                $stato = \JF\CoreBundle\Entity\Licenza::$S_SAL;
                $licenza->setSconto($sconto);
            }
            if($licenza->getMarket()) {
                if(!$market) {
                    $licenza->setMarket($market);
                    $stato = \JF\CoreBundle\Entity\Licenza::$S_DEL;
                }
            } else {
                $stato = \JF\CoreBundle\Entity\Licenza::$S_HID;
            }
            $licenza->setStato($stato);
            $this->persist($licenza);
        }
        return array('codice' => $licenza->getCodiceEsteso(), 'stato' => $licenza->getStatoTestuale());
    }

    protected function findLicenza(\JF\CoreBundle\Entity\Package $package, $gruppo, $sigla) {
        return $this->findOneBy('JFCoreBundle:Licenza', array('package' => $package->getId(), 'gruppo' => $gruppo, 'sigla' => $sigla));
    }

}
