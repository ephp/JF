<?php

namespace JF\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/__install")
 */
class RenderController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        Traits\InstallController;

    /**
     * @Route("-core")
     */
    public function indexAction() {
        $this->installPackage('jf.core', 'JF-System Core', 'Il core di JF-System, le cui regole devono essere base per lo sviluppo degli altri pacchetti');
    }

}
