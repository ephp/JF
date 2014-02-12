<?php

namespace JF\ACLBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Ephp\UtilityBundle\Controller\Traits\BaseController;
use JF\CoreBundle\Controller\Traits\InstallController as BaseInstall;

/**
 * @Route("/__sync")
 */
class SyncController extends Controller {

    use BaseController;

    /**
     * @Route("-acl/fetch", name="sync_acl_fetch"), defaults={"_format": "json"})
     * @Method("POST")
     */
    public function fetchAction() {
        $user = $this->getParam('user');
    }

    /**
     * @Route("-acl/push", name="sync_acl_push"), defaults={"_format": "json"})
     */
    public function pushAction() {
        
    }

}
