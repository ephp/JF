<?php

namespace JF\CoreBundle\DependencyInjection\Traits;

trait CoreExtension {

    protected function newPackage(&$package, $code, $name, $order, $permission) {
        $package[$code] = array('name' => $name, 'order' => $order, 'permission' => $permission);
    }
    
    protected function newRole(&$roles, $code, $abbr, $name) {
        $roles[$code] = array('name' => $name, 'code' => $code, 'abbr' => $abbr);
    }
    
    protected function newWidget(&$widgets, $key, $name, $roles, $render, $params = array()) {
        foreach ($roles as $role) {
            if(!isset($widgets[$role])) {
                $widgets[$role] = array();
            }
        }
        $widgets[$role][$key] = array('name' => $name, 'render' => $render, 'params' => $params);
    }

}
