<?php

namespace JF\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/__dispatcher")
 */
class DispatcherController extends Controller {

    /**
     * @Route("/", name="index")
     */
    public function indexAction() {
        return $this->redirect($this->generateUrl('fos_user_profile_show'));
    }

}
