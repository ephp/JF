<?php

namespace Claims\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ephp\UtilityBundle\Utility\Debug;
use Claims\HBundle\Entity\Countdown;

/**
 * @Route("/h/reminder")
 */
class ReminderController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\ACLBundle\Controller\Traits\NotifyController;

    /**
     * @Route("/daily-cron", name="reminder_daily", defaults={"_format"="json"})
     */
    public function dailyAction() {
        $output = array();
        $out = $this->executeSql("
SELECT c.id 
  FROM acl_clienti c, 
       acl_licenze cl, 
		 jf_licenze l, 
		 jf_gruppi_licenze gl, 
		 jf_package p 
 WHERE cl.cliente_id = c.id 
   AND cl.licenza_id = l.id 
	AND l.gruppo_id = gl.id 
	AND gl.package_id = p.id 
	AND p.sigla = 'cl.h' 
	AND gl.sigla = 'pratiche'
	AND l.params RLIKE 'daily_email[^b]+b:1'
        ");
        foreach ($out as $id) {
            $id = $id['id'];
            $cliente = $this->find('JFACLBundle:Cliente', $id);
            /* @var $cliente \JF\ACLBundle\Entity\Cliente */
            foreach ($cliente->getUtenze() as $gestore) {
                /* @var $gestore \JF\ACLBundle\Entity\Gestore */
                $output[$cliente->getNome()]['email'][$gestore->getSigla()] = $this->sendEmailAction($gestore);
            }
            
        }
        return $this->jsonResponse($output);
    }

    private function sendEmailAction(\JF\ACLBundle\Entity\Gestore $gestore) {
        $filtri = $this->buildFiltri($gestore);
        $pratiche = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();
        $countdown = $this->findBy('ClaimsHBundle:Countdown', array('stato' => 'A', 'gestore' => $gestore->getId()), array('sended_at' => 'ASC'));
        if (count($pratiche) > 0 || count($countdown) > 0) {
            $oggi = new \DateTime();
            $this->notify($gestore, "[JF-System] Claims-Hospital agenda di: {$gestore->getNome()} " . $oggi->format('d-m-Y'), 'ClaimsHBundle:email:daily', array('oggi' => $oggi, 'pratiche' => $pratiche, 'countdown' => $countdown));
        }
        return array('pratiche' => count($pratiche), 'countdown' => count($countdown));
    }
    
    private function buildFiltri(\JF\ACLBundle\Entity\Gestore $gestore) {
        $cliente = $gestore->getCliente();
        $filtri = array(
            'in' => array(
                'cliente' => $cliente->getId(),
            ),
            'out' => array(
            ),
        );
        $filtri['in']['gestore'] = $gestore->getId();
        $filtri['in']['evento'] = new \DateTime();
        $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
        $filtri['out']['dasc'] = null;
        $filtri['ricerca'] = array();
        $filtri['sorting'] = 'claimant';
        return $filtri;
    }

}

