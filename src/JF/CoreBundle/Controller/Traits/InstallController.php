<?php

namespace JF\CoreBundle\Controller\Traits;

trait InstallController {

    protected function findPackage($sigla) {
        return $this->findOneBy('JFCoreBundle:Package', array('sigla' => $sigla));
    }

    protected function findGruppo(\JF\CoreBundle\Entity\Package $package, $sigla) {
        return $this->findOneBy('JFCoreBundle:Gruppo', array('package' => $package->getId(), 'sigla' => $sigla));
    }

    protected function findLicenza(\JF\CoreBundle\Entity\Gruppo $gruppo, $sigla) {
        return $this->findOneBy('JFCoreBundle:Licenza', array('gruppo' => $gruppo->getId(), 'sigla' => $sigla));
    }

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

    protected function installGruppo($package, $sigla, $nome, $descrizione) {
        $package = $this->findPackage($package);
        $gruppo = $this->findGruppo($package, $sigla);
        if (!$gruppo) {
            $gruppo = new \JF\CoreBundle\Entity\Gruppo();
            $gruppo->setSigla($sigla)
                    ->setPackage($package)
                    ->setNome($nome)
                    ->setDescrizione($this->renderView($descrizione));
            $this->persist($gruppo);
        } else {
            $gruppo->setNome($nome)
                    ->setDescrizione($this->renderView($descrizione));
            $this->persist($gruppo);
        }
    }

    protected function newLicenza($package, $gruppo, $sigla, $ordine, $nome, $descrizione, $durata, $roles, $widgets, $params, $prezzo, $sconto = null, $autoinstall = false, $market = true) {
        $package = $this->findPackage($package);
        $gruppo = $this->findGruppo($package, $gruppo);
        $licenza = $this->findLicenza($gruppo, $sigla);
        if (!$licenza) {
            $licenza = new \JF\CoreBundle\Entity\Licenza();
            $licenza->setGruppo($gruppo)
                    ->setOrdine($ordine)
                    ->setSigla($sigla)
                    ->setNome($nome)
                    ->setDescrizione($this->renderView($descrizione))
                    ->setDurata($durata)
                    ->setRoles($roles)
                    ->setWidgets($widgets)
                    ->setParams($params)
                    ->setPrezzo($prezzo)
                    ->setSconto($sconto ? : $prezzo)
                    ->setAutoinstall($autoinstall)
                    ->setMarket($market)
                    ->setStato($sconto ? \JF\CoreBundle\Entity\Licenza::$S_LIM : \JF\CoreBundle\Entity\Licenza::$S_NEW)
            ;
            if (!$market) {
                $licenza->setStato(\JF\CoreBundle\Entity\Licenza::$S_HID);
            }
            $this->persist($licenza);
        } else {
            $stato = \JF\CoreBundle\Entity\Licenza::$S_NOP;
            $descrizione = $this->renderView($descrizione);
            if ($licenza->getDescrizione() != $descrizione) {
                $licenza->setDescrizione($descrizione);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if ($licenza->getOrdine() != $ordine) {
                $licenza->setOrdine($ordine);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if ($licenza->getNome() != $nome) {
                $licenza->setNome($nome);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if ($licenza->getDurata() != $durata) {
                $licenza->setDurata($durata);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if ($licenza->getAutoinstall() != $autoinstall) {
                $licenza->setAutoinstall($autoinstall);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if ($licenza->getRoles() != $roles) {
                $licenza->setRoles($roles);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if ($licenza->getParams() != $params) {
                $licenza->setParams($params);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if ($licenza->getWidgets() != $widgets) {
                $licenza->setWidgets($widgets);
                $stato = \JF\CoreBundle\Entity\Licenza::$S_UPD;
            }
            if ($licenza->getPrezzo() != $prezzo) {
                $stato = $licenza->getPrezzo() > $prezzo ? \JF\CoreBundle\Entity\Licenza::$S_DOW : \JF\CoreBundle\Entity\Licenza::$S_UPD;
                $licenza->setPrezzo($prezzo);
                $licenza->setSconto($prezzo);
            }
            if ($sconto) {
                $stato = \JF\CoreBundle\Entity\Licenza::$S_SAL;
                $licenza->setSconto($sconto);
            }
            if ($licenza->getMarket()) {
                if (!$market) {
                    $licenza->setMarket($market);
                    $stato = \JF\CoreBundle\Entity\Licenza::$S_DEL;
                }
            } else {
                $stato = \JF\CoreBundle\Entity\Licenza::$S_HID;
            }
            $licenza->setStato($stato);
            $this->persist($licenza);
        }

        if ($licenza->getAutoinstall()) {
            foreach ($this->findBy('JFACLBundle:Cliente', array()) as $cliente) {
                if (!$this->findOneBy('JFACLBundle:Licenza', array('gruppo' => $gruppo, 'cliente' => $cliente->getId()))) {
                    $_licenza = new \JF\ACLBundle\Entity\Licenza();
                    $_licenza->setLicenza($licenza);
                    $_licenza->setCliente($cliente);
                    $_licenza->setPagamento(new \DateTime());
                    $this->persist($_licenza);
                }
            }
        }

        return array('codice' => $licenza->getCodiceEsteso(), 'stato' => $licenza->getStatoTestuale());
    }

}
