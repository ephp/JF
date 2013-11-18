<?php

namespace JF\CoreBundle\DependencyInjection\Traits;

trait CoreExtension {

    public function configure(\Symfony\Component\DependencyInjection\ContainerBuilder $container) {
            $this->setPackage($container);
            $this->setMenu($container);
            $this->setRoles($container);
            $this->setWidgets($container);
            $this->setInstall($container);
    }
    
    protected function newPackage(&$package, $code, $name, $order, $permission) {
        $package[$code] = array('name' => $name, 'order' => $order, 'permission' => $permission);
    }
    
    protected function newRole(&$roles, $code, $abbr, $name) {
        $roles[$code] = array('name' => $name, 'code' => $code, 'abbr' => $abbr);
    }
    
    protected function newInstall(&$install, $route, $render) {
        $install[$route] = $render;
    }
    
    protected function newWidget(&$widgets, $key, $name, $roles, $render, $params = array()) {
        if($roles === true) {
            $roles = array('ROLE_USER');
        }
        foreach ($roles as $role) {
            if(!isset($widgets[$role])) {
                $widgets[$role] = array();
            }
            $widgets[$role][$key] = array('name' => $name, 'render' => $render, 'params' => $params);
        }
    }

}
