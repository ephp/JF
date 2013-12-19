<?php

namespace JF\GitHubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/github")
 * @Template()
 */
class DefaultController extends Controller {

    use \Ephp\ACLBundle\Controller\Traits\NotifyController,
        \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * @Route("/webhook", defaults={"_format": "josn"})
     * @Method("POST")
     */
    public function indexAction() {
        $request = $this->getRequest();
        
        $user = $this->find('JFACLBundle:Gestore', 1);
        $out = array(
            'type' => \Ephp\UtilityBundle\Utility\Debug::typeof($request->getContent()),
            'content' => $request->getContent(),
        );
        $this->notify($user, 'Test GitHub', 'JFGitHubBundle:email:test', $out);

        return $this->jsonResponse($out);
    }

}
