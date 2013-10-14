<?php

namespace Claims\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ephp\UtilityBundle\Utility\Debug;
use Claims\HBundle\Entity\Countdown;

/**
 * @Route("/h/countdown")
 */
class CountdownController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * Lists all Gestore entities.
     *
     * @Route("/", name="claims_h_countdown", options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     * @Template()
     */
    public function indexAction() {
        $gestore = $this->getUser();
        $cliente = $gestore->getCliente();
        $nuovi = false;
        $aperti = false;
        $chiusi = false;
        $miei_aperti = false;
        $miei_chiusi = false;
        if($this->getUser()->hasRole('C_ADMIN')) {
            $nuovi = $this->findBy('ClaimsHBundle:Countdown', array('cliente' => $cliente->getId(), 'stato' => 'N'), array('sended_at' => 'ASC'));
            $aperti = $this->findBy('ClaimsHBundle:Countdown', array('cliente' => $cliente->getId(), 'stato' => 'A'), array('sended_at' => 'ASC'));
            $chiusi = $this->findBy('ClaimsHBundle:Countdown', array('cliente' => $cliente->getId(), 'stato' => 'C'), array('sended_at' => 'DESC'));
        }
        if($this->getUser()->hasRole(array('C_GESTORE_H', 'C_RECUPERI_H'))) {
            $miei_aperti = $this->findBy('ClaimsHBundle:Countdown', array('cliente' => $cliente->getId(), 'stato' => 'A', 'gestore' => $gestore->getId()), array('sended_at' => 'ASC'));
            $miei_chiusi = $this->findBy('ClaimsHBundle:Countdown', array('cliente' => $cliente->getId(), 'stato' => 'C', 'gestore' => $gestore->getId()), array('sended_at' => 'DESC'));
        }

        return array(
            'gestore' => $gestore,
            'nuovi' => $nuovi,
            'aperti' => $aperti,
            'chiusi' => $chiusi,
            'miei_aperti' => $miei_aperti,
            'miei_chiusi' => $miei_chiusi,
            'gestori' => $this->findBy('JFACLBundle:Gestore', array(), array('sigla' => 'ASC')),
        );
    }

    /**
     * Lists all Gestore entities.
     *
     * @Route("/countdown-gestore", name="claims_h_countdown_assegna_gestore", defaults={"_format"="json"}, options={"expose"=true, "ACL": {"in_role": {"C_ADMIN"}}})
     * @Template()
     */
    public function assegnaGestoreCountdownAction() {
        $req = $this->getRequest()->get('cd');

        $cd = $this->find('ClaimsHBundle:Countdown', $req['id']);
        /* @var $cd Countdown */
        $gestore = $this->findOneBy('JFACLBundle:Gestore', array('sigla' => $req['gestore']));
        /* @var $gestore \JF\ACLBundle\Entity\Gestore */

        $genera = is_null($cd->getGestore());
        try {
            $cd->setGestore($gestore);
            $cd->setStato('A');
            $this->persist($cd);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this->jsonResponse(array('redirect' => $this->generateUrl('claims_h_countdown')));
    }

    /**
     * Lists all Gestore entities.
     *
     * @Route("/countdown-delete/{id}", name="claims_h_countdown_delete", options={"ACL": {"in_role": {"C_ADMIN"}}})
     * @Template()
     */
    public function cancellaCountdownAction($id) {
        $cd = $this->find('ClaimsHBundle:Countdown', $id);
        /* @var $cd Countdown */
        try {
            $email = $cd->getEmail();
            $this->remove($cd);
            $this->remove($email);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this->redirect($this->generateUrl('claims_h_countdown'));
    }


    /**
     * Lists all Scheda entities.
     *
     * @Route("-countdown-reply/{id}", name="claims_h_countdown_reply", options={"ACL": {"in_role": {"C_ADMIN", "C_GESTORE_H", "C_RECUPERI_H"}}})
     */
    public function replyAction($id) {
        $req = $this->getParam('email');
        $gestore = $this->getUser();
        /* @var $gestore \JF\ACLBundle\Entity\Gestore */
        $countdown = $this->find('ClaimsHBundle:Countdown', $id);
        /* @var $countdown Countdown */
        $docs = json_decode($req['docs']);
        $message = \Swift_Message::newInstance()
                ->setSubject("RE: " . $countdown->getEmail()->getSubject() . " [RECD-{$countdown->getId()}]")
                ->setFrom($this->container->getParameter('email_robot'))
                ->setTo(trim($countdown->getEmail()->getFromAddress()))
                ->setCc(trim($gestore->getEmail()))
                ->setBcc(trim($this->container->getParameter('email_robot')))
                ->setReplyTo(trim($gestore->getEmail()), $gestore->getFullName())
                ->setBody($this->renderView("ClaimsHBundle:email:risposta_countdown.txt.twig", array('gestore' => $gestore, 'testo' => $req['testo'], 'allegati' => $docs)))
                ->addPart($this->renderView("ClaimsHBundle:email:risposta_countdown.html.twig", array('gestore' => $gestore, 'testo' => $req['testo'], 'allegati' => $docs)), 'text/html');
        ;
        foreach ($docs as $doc) {
            $message->attach(\Swift_Attachment::fromPath($this->dir() . $doc));
        }

        
        $message->getHeaders()->addTextHeader('X-Mailer', 'PHP v' . phpversion());
        $this->get('mailer')->send($message);
        
        $countdown->setStato('C');
        $countdown->setResponsedAt(new \DateTime());
        if(!$countdown->getGestore()) {
            $countdown->setGestore($gestore);
        }
        $this->persist($countdown);
        
        return $this->redirect($this->generateUrl('claims_h_countdown'));
    }


}

