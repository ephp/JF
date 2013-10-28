<?php

namespace JF\CalendarBundle\DependencyInjection\Traits;

use \JF\CoreBundle\DependencyInjection\Traits\CoreExtension;

trait CalendarExtension {

    use CoreExtension {
        CoreExtension::configure as configureCore;
    }
    
    public function configure(\Symfony\Component\DependencyInjection\ContainerBuilder $container) {
        $this->configureCore($container);
        $this->setTipoEventi($container);
    }
    
    protected function newTipoEvento(&$tipoEvento, $code, $name, $colore, $cancellabile = true, $modificabile = true, $pubblico = true, $permission = array()) {
        $tipoEvento[$code] = array('name' => $name, 'colore' => $colore, 'cancellabile' => $cancellabile, 'modificabile' => $modificabile, 'pubblico' => $pubblico, 'permission' => $permission);
    }

    protected function addLicenzaTipoEvento(&$tipoEvento, $code, $permission) {
        if(isset($tipoEvento[$code])) {
            if(is_array($tipoEvento[$code]['permission'])) {
                $tipoEvento[$code]['permission'][] = $permission;
            } else {
                $tipoEvento[$code]['permission'] = array($permission);
            }
        }
    }
    
}
