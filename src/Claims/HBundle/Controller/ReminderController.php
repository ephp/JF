<?php

namespace Claims\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ephp\UtilityBundle\Utility\Debug;
use Ephp\UtilityBundle\Utility\Time;

/**
 * @Route("/h/reminder")
 */
class ReminderController extends Controller {

    use \Ephp\UtilityBundle\Controller\Traits\BaseController,
        \Ephp\ACLBundle\Controller\Traits\NotifyController,
        Traits\CalendarController;

    /**
     * @Route("/30gg-cron", name="reminder_30gg", defaults={"_format"="json"})
     */
    public function verificaAction() {
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
	AND l.params RLIKE '30day_verify[^b]+b:1'
        ");
        foreach ($out as $id) {
            $id = $id['id'];
            $cliente = $this->find('JFACLBundle:Cliente', $id);
            /* @var $cliente \JF\ACLBundle\Entity\Cliente */
            $output[$cliente->getNome()]['verifiche'] = $this->creaVerifiche($cliente);
            
        }
        return $this->jsonResponse($output);
    }

    private function creaVerifiche(\JF\ACLBundle\Entity\Cliente $cliente) {
        $out = $this->executeSql("
SELECT e.id,
       e.pratica_id,
       max(e.delta_g) as delta_g,
       max(e.data_ora),
       DATEDIFF(NOW(), max(e.data_ora)) as days
  FROM claims_h_eventi e
  LEFT JOIN cal_tipi t
    ON t.id = e.tipo_id
  LEFT JOIN claims_h_pratiche p
    ON p.id = e.pratica_id
  LEFT JOIN claims_priorita pr
    ON pr.id = p.priorita_id
 WHERE e.cliente_id = {$cliente->getId()} 
   AND t.sigla IN ('VER', 'ASC')
   AND pr.priorita != 'Chiuso'
 GROUP BY e.pratica_id
HAVING days >= 30 
        ");
        $pratiche = $verifiche = 0;
        foreach ($out as $row) {
            $pratiche++;
            $pratica = $this->find('ClaimsHBundle:pratica', $row['pratica_id']);
            /* @var $pratica \Claims\HBundle\Entity\Pratica */
            $data = \DateTime::createFromFormat('Y-m-d h:i:s', $row['data_ora']);
            /* @var $data \DateTime */
            $data->setTime(8, 0, 0);
            while($row['days'] > 30) {
                $verifiche++;
                $row['days'] -= 30;
                $row['delta_g'] += 30;
                $data = Time::calcolaData($data, 30);
                $evento = $this->newEvento($this->VERIFICA_PERIODICA, $pratica, 'Verifica');
                $evento->setDeltaG($row['delta_g']);
                $evento->setDataOra($data);
                $this->persist($evento);
            }
        }
        return array('pratiche' => $pratiche, 'verifiche' => $verifiche);
    }
    
    private function buildFiltriVerifice(\JF\ACLBundle\Entity\Gestore $gestore) {
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
        $filtri = $this->buildFiltriGiorno($gestore);
        $pratiche = $this->getRepository('ClaimsHBundle:Pratica')->filtra($filtri)->getQuery()->execute();
        
        $countdown = $this->findBy('SLCHBundle:Countdown', array('stato' => 'A', 'gestore' => $gestore->getId()), array('sended_at' => 'ASC'));
        if (count($pratiche) > 0 || count($countdown) > 0) {
            $oggi = new \DateTime();
            $this->notify($gestore, "[JF-System] Claims-Hospital agenda di: {$gestore->getNome()} " . $oggi->format('d-m-Y'), 'ClaimsHBundle:email:daily', array('oggi' => $oggi, 'pratiche' => $pratiche, 'countdown' => $countdown));
        }
        return array('pratiche' => count($pratiche), 'countdown' => count($countdown));
    }
    
    private function buildFiltriGiorno(\JF\ACLBundle\Entity\Gestore $gestore) {
        $cliente = $gestore->getCliente();
        $filtri = array(
            'in' => array(
                'cliente' => $cliente->getId(),
            ),
            'out' => array(
            ),
        );
        $filtri['in']['evento'] = new \DateTime();
        if($gestore->hasRole('C_RECUPERI_H')) {
            $filtri['in']['evento_recupero'] = $gestore->getId();
        } else {
            $filtri['in']['gestore'] = $gestore->getId();
        }
        $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
        $filtri['out']['dasc'] = null;
        $filtri['ricerca'] = array();
        $filtri['sorting'] = 'claimant';
        return $filtri;
    }

}

