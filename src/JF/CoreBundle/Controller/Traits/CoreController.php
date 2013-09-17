<?php

namespace JF\CoreBundle\Controller\Traits;

trait CoreController {

    protected function userWidget() {
        $user_widgets = array();
        $widgets = $this->container->getParameter('jf.widgets');
        foreach ($this->getUser()->getRoles() as $role) {
            if (isset($widgets[$role])) {
                foreach ($widgets[$role] as $key => $widget) {
                    $user_widgets[$key] = $widget;
                }
            }
        }
        return $user_widgets;
    }
    
    protected function roles() {
        return $this->container->getParameter('jf.roles');
    }

}
