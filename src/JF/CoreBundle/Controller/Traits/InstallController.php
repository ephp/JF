<?php

namespace JF\CoreBundle\Controller\Traits;

trait InstallController {

    protected function installPackage($sigla, $nome, $descrizione) {
        if(!$this->findPackage($sigla)) {
            $package = new \JF\CoreBundle\Entity\Package();
            $package->setSigla($sigla);
            $package->setNome($nome);
            $package->setDescrizione($descrizione);
            $this->persist($package);
        }
    }
    
    protected function findPackage($sigla) {
        return $this->findOneBy('JFCoreBundle:Package', array('sigla' => $sigla));
    }

}
