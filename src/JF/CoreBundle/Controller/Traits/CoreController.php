<?php

namespace JF\CoreBundle\Controller\Traits;

trait CoreController {

    protected function userWidget() {
        return $this->widgets();
    }

    protected function roles($all = false) {
        if ($all || !$this->getUser()->getCliente()) {
            return $this->container->getParameter('jf.roles');
        }
        $cliente = $this->getUser()->getCliente();
        $roles = array();
        $jf_roles = $this->container->getParameter('jf.roles');
        foreach ($cliente->getRoles() as $role) {
            $roles[$role] = $jf_roles[$role];
        }
        return $roles;
    }

    protected function widgets($user = true, $all = false) {
        if ($all) {
            return $this->container->getParameter('jf.widgets');
        }
        $widgets = array();
        $jf_widgets = $this->container->getParameter('jf.widgets');
        if ($user) {
            $roles = $this->getUser()->getRoles();
            $cliente = $this->getUser()->getCliente();
            if (!$cliente) {
                foreach ($roles as $role) {
                    if (isset($jf_widgets[$role])) {
                        foreach ($jf_widgets as $widget) {
                            $widgets[$widget] = $jf_widgets[$role][$widget];
                        }
                    }
                }
            } else {
                foreach ($cliente->getWidgets() as $widget) {
                    foreach ($roles as $role) {
                        if (isset($jf_widgets[$role])) {
                            $widgets[$widget] = $jf_widgets[$role][$widget];
                        }
                    }
                }
            }
        } else {
            $cliente = $this->getUser()->getCliente();
            if (!$cliente) {
                foreach ($this->roles(true) as $role) {
                    if (isset($jf_widgets[$role])) {
                        foreach ($jf_widgets as $widget) {
                            $widgets[$widget] = $jf_widgets[$role][$widget];
                        }
                    }
                }
            } else {
                foreach ($cliente->getWidgets() as $widget) {
                    foreach ($cliente->getRoles() as $role) {
                        if (isset($jf_widgets[$role])) {
                            $widgets[$widget] = $jf_widgets[$role][$widget];
                        }
                    }
                }
            }
        }
        return $widgets;
    }

}
