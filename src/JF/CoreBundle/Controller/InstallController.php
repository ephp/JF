<?php

namespace JF\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use JF\CoreBundle\Controller\Traits\InstallController as BaseInstall;

/**
 * @Route("/__install")
 */
class InstallController extends Controller {

    use BaseController,
        BaseInstall;

    /**
     * @Route("-core", name="install_core", defaults={"_format": "json"})
     */
    public function indexAction() {
        $package = 'jf.core';
        $status = 200;
        $message = 'Ok';
        $licenze = array();
        try {
            $this->getEm()->beginTransaction();
            
            $this->installPackage($package, 'JF-System Core', 'JFCoreBundle:Install:package.txt.twig');

            $this->getEm()->commit();
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            $status = 500;
            $message = $e->getMessage();
        }
        
        return array(
            'package' => $package,
            'status' => $status,
            'message' => $message,
            'licenze' => $licenze,
        );
    }

}
