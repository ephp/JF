<?php

namespace JF\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/")
 */
class CorporateController extends Controller {

    /**
     * @Route("/", name="home")
     * @Route("/", name="homepage")
     */
    public function indexAction() {
        if($this->getUser()) {
            return $this->redirect($this->generateUrl('index'));
        } else {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
    }

}
