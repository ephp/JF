<?php

namespace JF\ACLBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Gestore controller.
 *
 * @Route("/profilo")
 */
class ProfiloController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController; //,
//        \Ephp\Bundle\DragDropBundle\Controller\Traits\DragDropController,
//        \Ephp\ImapBundle\Controller\Traits\ImapController;

    /**
     * Lists all Gestore entities.
     *
     * @Route("/", name="profilo")
     * @Template()
     */
    public function indexAction() {
        $gestore = $this->getUser();
        $priorita = array(
            $this->findOneBy('EphpSinistriBundle:Priorita', array('priorita' => 'attenzione'))->getId(),
            $this->findOneBy('EphpSinistriBundle:Priorita', array('priorita' => 'alta'))->getId(),
            $this->findOneBy('EphpSinistriBundle:Priorita', array('priorita' => 'assegnato'))->getId(),
        );
        $priorita_new = array(
            $this->findOneBy('EphpSinistriBundle:Priorita', array('priorita' => 'nuovo'))->getId(),
        );
        $priorita_rea = array(
            $this->findOneBy('EphpSinistriBundle:Priorita', array('priorita' => 'riattivato'))->getId(),
        );
        $prima_pagina = $this->findBy('EphpSinistriBundle:Scheda', array('gestore' => $gestore->getId(), 'prima_pagina' => true), array('claimant' => 'ASC'));
        $attenzione = $this->findBy('EphpSinistriBundle:Scheda', array('gestore' => $gestore->getId(), 'priorita' => $priorita), array('claimant' => 'ASC'));
        $nuove = $this->findBy('EphpSinistriBundle:Scheda', array('priorita' => $priorita_new), array('claimant' => 'ASC'));
        $riattivate = $this->findBy('EphpSinistriBundle:Scheda', array('priorita' => $priorita_rea), array('claimant' => 'ASC'));

        $tabs = $this->findBy('EphpSinistriBundle:StatoOperativo', array('tab' => true));
        $private = $pubbliche = array();
        foreach ($tabs as $tab) {
            $pu = $pr = array();
            $all = $this->findBy('EphpSinistriBundle:Scheda', array('stato_operativo' => $tab->getId()), array('claimant' => 'ASC'));
            foreach ($all as $one) {
                /* @var $one \Ephp\Bundle\SinistriBundle\Entity\Scheda */
                if ($one->getGestore()->getId() == $gestore->getId()) {
                    $pr[] = $one;
                } else {
                    $pu[] = $one;
                }
            }

            $private[$tab->getId()] = $pr;
            $pubbliche[$tab->getId()] = $pu;
        }
        $prioritas = $this->findBy('EphpSinistriBundle:Priorita', array());
        $stati_operativi = $this->findBy('EphpSinistriBundle:StatoOperativo', array());
        
        return array(
            'tabs' => $tabs,
            'private' => $private,
            'pubbliche' => $pubbliche,
            'gestore' => $gestore,
            'oggi' => new \DateTime(),
            'prima_pagina' => $prima_pagina,
            'attenzione' => $attenzione,
            'nuove' => $nuove,
            'riattivate' => $riattivate,
            'priorita' => $prioritas,
            'stati_operativi' => $stati_operativi,
            'gestori' => $this->findBy('EphpGestoriBundle:Gestore', array(), array('sigla' => 'ASC')),
        );
    }

    /**
     * Lists all Gestore entities.
     *
     * @Route("/ritardi", name="ritardi")
     * @Template()
     */
    public function ritardiAction() {

        $gestore = $this->getUser();
        $miei_ritardi = $this->getRepository('EphpSinistriBundle:Scheda')->ritardi($gestore->getId());
        $tutti_ritardi = $this->getRepository('EphpSinistriBundle:Scheda')->ritardi();

        $gestori = $this->findBy('EphpGestoriBundle:Gestore', array(), array('sigla' => 'ASC'));
        $priorita = $this->findBy('EphpSinistriBundle:Priorita', array());
        $stati_operativi = $this->findBy('EphpSinistriBundle:StatoOperativo', array());
        
        return array(
            'gestore' => $gestore,
            'miei_ritardi' => $miei_ritardi,
            'tutti_ritardi' => $tutti_ritardi,
            'gestori' => $gestori,
            'priorita' => $priorita,
            'stati_operativi' => $stati_operativi,
        );
    }

    /**
     * Lists all Gestore entities.
     *
     * @Route("/countdown-gestore", name="countdown_assegna_gestore", defaults={"_format"="json"})
     * @Template()
     */
    public function assegnaGestoreCountdownAction() {
        $req = $this->getRequest()->get('cd');

        $cd = $this->find('EphpEmailBundle:Countdown', $req['id']);
        /* @var $cd \Ephp\Bundle\EmailBundle\Entity\Countdown */
        $gestore = $this->findOneBy('EphpGestoriBundle:Gestore', array('sigla' => $req['gestore']));
        /* @var $gestore \JF\ACLBundle\Entity\Gestore */

        $genera = is_null($cd->getGestore());
        try {
            $cd->setGestore($gestore);
            $cd->setStato('A');
            $this->persist($cd);
        } catch (\Exception $e) {
            throw $e;
        }
        return new \Symfony\Component\HttpFoundation\Response(json_encode(array('redirect' => $this->generateUrl('countdown'))));
    }

    /**
     * Lists all Gestore entities.
     *
     * @Route("/countdown-delete/{id}", name="countdown_delete")
     * @Template()
     */
    public function cancellaCountdownAction($id) {
        $cd = $this->find('EphpEmailBundle:Countdown', $id);
        /* @var $cd \Ephp\Bundle\EmailBundle\Entity\Countdown */
        try {
            $email = $cd->getEmail();
            $this->remove($cd);
            $this->remove($email);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this->redirect($this->generateUrl('countdown'));
    }

    /**
     * Lists all Gestore entities.
     *
     * @Route("/countdown", name="countdown")
     * @Template()
     */
    public function countdownAction() {
        $gestore = $this->getUser();
        $nuovi = $this->findBy('EphpEmailBundle:Countdown', array('stato' => 'N'), array('sended_at' => 'ASC'));
        $aperti = $this->findBy('EphpEmailBundle:Countdown', array('stato' => 'A'), array('sended_at' => 'ASC'));
        $miei_aperti = $this->findBy('EphpEmailBundle:Countdown', array('stato' => 'A', 'gestore' => $gestore->getId()), array('sended_at' => 'ASC'));
        $chiusi = $this->findBy('EphpEmailBundle:Countdown', array('stato' => 'C'), array('sended_at' => 'DESC'));
        $miei_chiusi = $this->findBy('EphpEmailBundle:Countdown', array('stato' => 'C', 'gestore' => $gestore->getId()), array('sended_at' => 'DESC'));

        return array(
            'gestore' => $gestore,
            'nuovi' => $nuovi,
            'aperti' => $aperti,
            'chiusi' => $chiusi,
            'miei_aperti' => $miei_aperti,
            'miei_chiusi' => $miei_chiusi,
            'gestori' => $this->findBy('EphpGestoriBundle:Gestore', array(), array('sigla' => 'ASC')),
        );
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-countdown-reply/{id}", name="countdown_reply")
     */
    public function replyAction($id) {
        $req = $this->getParam('email');
        $gestore = $this->getUser();
        /* @var $gestore \JF\ACLBundle\Entity\Gestore */
        $countdown = $this->find('EphpEmailBundle:Countdown', $id);
        /* @var $countdown \Ephp\Bundle\EmailBundle\Entity\Countdown */
        $docs = json_decode($req['docs']);
        $message = \Swift_Message::newInstance()
                ->setSubject("RE: " . $countdown->getEmail()->getSubject() . " [RECD-{$countdown->getId()}]")
                ->setFrom($this->container->getParameter('email_robot'))
                ->setTo(trim($countdown->getEmail()->getFromAddress()))
                ->setCc(trim($gestore->getEmail()))
                ->setBcc(trim($this->container->getParameter('email_robot')))
                ->setReplyTo(trim($gestore->getEmail()), $gestore->getFullName())
                ->setBody($this->renderView("EphpEmailBundle:email:risposta_countdown.txt.twig", array('gestore' => $gestore, 'testo' => $req['testo'], 'allegati' => $docs)))
                ->addPart($this->renderView("EphpEmailBundle:email:risposta_countdown.html.twig", array('gestore' => $gestore, 'testo' => $req['testo'], 'allegati' => $docs)), 'text/html');
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
        
        return $this->redirect($this->generateUrl('countdown'));
    }

    /**
     * Lists all Scheda entities.
     *
     * @Route("-uploadMulti", name="email_upload_multi")
     * @Template("EphpDragDropBundle:DragDrop:multi.html.php")
     */
    public function uploadMultiAction() {
        return $this->multiFile();
    }

}
