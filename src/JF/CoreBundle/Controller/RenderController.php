<?php

namespace JF\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/__render")
 */
class RenderController extends Controller {

    /**
     * @Route("/__menu")
     * @Template()
     */
    public function menuAction() {
        $this->getDoctrine();
        
        return array(
            'selected' => $this->getRequest()->get('_route', 'home'),
            'menu'     => $this->generaMenu(),
        );
    }
    
    private function generateMenu() {
        $user = $this->getUser();
        $this->container->getParameter('jf.menu');
    }

}
