<?php

namespace Claims\HBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ephp\UtilityBundle\Utility\Debug;
use Claims\HBundle\Entity\Countdown;

/**
 * @Route("/cron-email")
 */
class CronEmailController extends Controller {

    use \Ephp\ImapBundle\Controller\Traits\ImapController,
        \Ephp\UtilityBundle\Controller\Traits\BaseController,
        Traits\CalendarController;

    const TPA = "/[a-z]{2}[a-z0-9]{1}( [a-z.]+[a-z]{1})?(\/|\-)[0-9]{2}\/[0-9]{1,3}/i";
    const RECD = "/RECD\-[0-9]+/i";

    /**
     * @Route("/countdown-cron", name="imap_countdown_cron", defaults={"_format"="json"})
     */
    public function countdownAction() {
        $output = array();
        $out = $this->executeSql("SELECT id FROM acl_clienti WHERE dati LIKE '%cl_h-pratiche%'");
        foreach ($out as $id) {
            $id = $id['id'];
            $cliente = $this->find('JFACLBundle:Cliente', $id);
            /* @var $cliente \JF\ACLBundle\Entity\Cliente */
            $dati = $cliente->getDati();
            $server = $dati['cl_h-pratiche'];
            $this->openImap($server['server'], $server['port'], $server['protocol'], $server['username'], $server['password'], $server['label_cd_richieste']);
            $n = $this->countMessages();
            $output[$i] = array(
                'countdown' => $n,
                'subjects' => array(),
            );
            for ($i = 1; $i <= $n; $i++) {
                try {
                    $this->getEm()->beginTransaction();
                    $body = $this->getBody($i);
                    $output[$i]['subjects'][] = $body->getSubject();
                    $header = $body->getHeader();
                    $this->persist($header);
                    $body->setHeader($header);
                    $attachs = $body->getAttach();
//                $body->getAttach()->clear();
                    if (false) {
                        foreach ($attachs as $attach) {
                            /* @var $attach \Ephp\ImapBundle\Entity\Attach */
                            $attach->setData('');
                            $body->addAttach($attach);
                        }
                    }
                    $this->persist($body);
                    foreach ($attachs as $attach) {
                        /* @var $attach \Ephp\ImapBundle\Entity\Attach */
                        $attach->setBody($body);
                        $this->persist($attach);
                    }
                    $countdown = new Countdown();
                    $countdown->setCliente($cliente);
                    $pratica = $this->findPratica($body);
                    $countdown->setEmail($header);
                    $countdown->setSendedAt($header->getDate());
                    $countdown->setScheda($pratica);
                    $countdown->setStato('N');
                    $this->persist($countdown);
                    $this->getEm()->commit();
                } catch (\Exception $e) {
                    $this->getEm()->rollback();
                    throw $e;
                }
            }
            for ($i = $n; $i > 0; $i--) {
                $this->deteteEmail($i);
            }
            $this->closeImap();
        }
        return $this->jsonResponse($output);
    }

    /**
     * @Route("/risposte-cron", name="imap_risposte_cron", defaults={"_format"="json"})
     */
    public function risposteAction() {
        $output = array();
        $out = $this->executeSql("SELECT id FROM acl_clienti WHERE dati LIKE '%cl_h-pratiche%'");
        foreach ($out as $id) {
            $id = $id['id'];
            $cliente = $this->find('JFACLBundle:Cliente', $id);
            /* @var $cliente \JF\ACLBundle\Entity\Cliente */
            $dati = $cliente->getDati();
            $server = $dati['cl_h-pratiche'];
            $this->openImap($server['server'], $server['port'], $server['protocol'], $server['username'], $server['password'], $server['label_cd_risposte']);
            $n = $this->countMessages();
            $output[$i] = array(
                'risposte' => $n,
                'subjects' => array(),
            );
            for ($i = 1; $i <= $n; $i++) {
                try {
                    $this->getEm()->beginTransaction();
                    $body = $this->getBody($i);
                    $output[$i]['subjects'][] = $body->getSubject();
                    $subject = $body->getSubject();
                    $s = $countdown = null;
                    preg_match(self::RECD, $subject, $s);
                    if (isset($s[0])) {
                        $token = substr($s[0], 5);
                        $countdown = $this->find('EphpEmailBundle:Countdown', $token);
                    } else {
                        break;
                    }
                    /* @var $countdown \Ephp\Bundle\EmailBundle\Entity\Countdown */
                    $header = $body->getHeader();
                    $this->persist($header);
                    $body->setHeader($header);
                    $attachs = $body->getAttach();
//                $body->getAttach()->clear();
                    if (false) {
                        foreach ($attachs as $attach) {
                            /* @var $attach \Ephp\ImapBundle\Entity\Attach */
                            $attach->setData('');
                            $body->addAttach($attach);
                        }
                    }
                    $this->persist($body);
                    foreach ($attachs as $attach) {
                        /* @var $attach \Ephp\ImapBundle\Entity\Attach */
                        $attach->setBody($body);
                        $this->persist($attach);
                    }
                    $countdown->setRisposta($header);
                    $this->persist($countdown);
                    $this->getEm()->commit();
                } catch (\Exception $e) {
                    $this->getEm()->rollback();
                    throw $e;
                }
            }
            for ($i = $n; $i > 0; $i--) {
                $this->deteteEmail($i);
            }
            $this->closeImap();
        }
        return $this->jsonResponse($output);
    }

    /**
     * @Route("/tpa-cron", name="imap_tpa", defaults={"_format"="json"})
     */
    public function tpaAction() {
        $output = array();
        $out = $this->executeSql("SELECT id FROM acl_clienti WHERE dati LIKE '%cl_h-pratiche%'");
        foreach ($out as $id) {
            $id = $id['id'];
            $cliente = $this->find('JFACLBundle:Cliente', $id);
            /* @var $cliente \JF\ACLBundle\Entity\Cliente */
            $dati = $cliente->getDati();
            $server = $dati['cl_h-pratiche'];
            $output[$id] = array(
                'contec' => 0,
                'contec_subjects' => array(),
                'ravinale' => 0,
                'ravinale_subjects' => array(),
                'email' => 0,
                'email_subjects' => array(),
            );

            $this->openImap($server['server'], $server['port'], $server['protocol'], $server['username'], $server['password'], $server['label_contec']);
            $n = $this->countMessages();
            $output[$id]['contec'] = $n;
            for ($i = 1; $i <= $n; $i++) {
                try {
                    $this->getEm()->beginTransaction();
                    $body = $this->getBody($i);
                    $output[$id]['contec_subjects'][] = $body->getSubject();
                    $scheda = $this->findPratica($body, self::CLAIMANT_CONTEC);
                    if ($scheda) {
                        if (is_array($scheda)) {
                            $schede = $scheda;
                            foreach ($schede as $scheda) {
                                $evento = $this->newEvento($this->EMAIL_JWEB, $scheda, $body->getSubject(), $body->getTxt());
                                $evento->setDataOra($body->getHeader()->getDate());
                                $this->persist($evento);
                            }
                        } else {
                            $evento = $this->newEvento($this->EMAIL_JWEB, $scheda, $body->getSubject(), $body->getTxt());
                            $evento->setDataOra($body->getHeader()->getDate());
                            $this->persist($evento);
                        }
                    }
                    $this->getEm()->commit();
                } catch (\Exception $e) {
                    $this->getEm()->rollback();
                    throw $e;
                }
            }
            for ($i = $n; $i > 0; $i--) {
                $this->deteteEmail($i);
            }
            $this->closeImap();

            $this->openImap($server['server'], $server['port'], $server['protocol'], $server['username'], $server['password'], $server['label_ravinale']);
            $n = $this->countMessages();
            $output[$id]['ravinale'] = $n;
            for ($i = 1; $i <= $n; $i++) {
                try {
                    $this->getEm()->beginTransaction();
                    $body = $this->getBody($i);
                    $output[$id]['ravinale_subjects'][] = $body->getSubject();
                    $scheda = $this->findPratica($body, self::CLAIMANT_RAVINALE);
                    if ($scheda) {
                        if (is_array($scheda)) {
                            $schede = $scheda;
                            foreach ($schede as $scheda) {
                                $evento = $this->newEvento($this->EMAIL_RAVINALE, $scheda, $body->getSubject(), $body->getTxt());
                                $evento->setDataOra($body->getHeader()->getDate());
                                $this->persist($evento);
                            }
                        } else {
                            $evento = $this->newEvento($this->EMAIL_RAVINALE, $scheda, $body->getSubject(), $body->getTxt());
                            $evento->setDataOra($body->getHeader()->getDate());
                            $this->persist($evento);
                        }
                    }
                    $this->getEm()->commit();
                } catch (\Exception $e) {
                    $this->getEm()->rollback();
                    throw $e;
                }
            }
            for ($i = $n; $i > 0; $i--) {
                $this->deteteEmail($i);
            }
            $this->closeImap();

            $this->openImap($server['server'], $server['port'], $server['protocol'], $server['username'], $server['password'], $server['label_manuala']);
            $n = $this->countMessages();
            $output[$id]['email'] = $n;
            for ($i = 1; $i <= $n; $i++) {
                try {
                    $this->getEm()->beginTransaction();
                    $body = $this->getBody($i);
                    $output[$id]['email_subjects'][] = $body->getSubject();
                    $scheda = $this->findPratica($body, self::CLAIMANT_TUTTI);
                    if ($scheda) {
                        if (is_array($scheda)) {
                            $schede = $scheda;
                            foreach ($schede as $scheda) {
                                $evento = $this->newEvento($this->EMAIL_MANUALE, $scheda, $body->getSubject(), $body->getTxt());
                                $evento->setDataOra($body->getHeader()->getDate());
                                $this->persist($evento);
                            }
                        } else {
                            $evento = $this->newEvento($this->EMAIL_MANUALE, $scheda, $body->getSubject(), $body->getTxt());
                            $evento->setDataOra($body->getHeader()->getDate());
                            $this->persist($evento);
                        }
                    }
                    $this->getEm()->commit();
                } catch (\Exception $e) {
                    $this->getEm()->rollback();
                    throw $e;
                }
            }
            for ($i = $n; $i > 0; $i--) {
                $this->deteteEmail($i);
            }
            $this->closeImap();
        }
        return new \Symfony\Component\HttpFoundation\Response(json_encode($output));
    }

    const CLAIMANT_CONTEC = "Contec";
    const CLAIMANT_RAVINALE = "Ravinale";
    const CLAIMANT_TUTTI = 'ALL';

    private function findPratica(\Ephp\ImapBundle\Entity\Body $body, $claimant = null) {
        $subject = $body->getSubject();
        $tpa = null;
        preg_match(self::TPA, $subject, $tpa);
        if (!$tpa) {
            $txt = $body->getTxt();
            preg_match(self::TPA, $txt, $tpa);
        }
        if (isset($tpa[0])) {
            $token = explode('/', $tpa[0]);
            if (count($token) == 2) {
                $token = array_merge(explode('-', $token[0]), array($token[1]));
            }
            $ospedale = $this->findOneBy('ClaimsHBundle:Ospedale', array('sigla' => str_replace(array('TPA ', 'tpa ', 'REQ ', 'req '), array('', '', '', ''), $token[0])));
            if (!$ospedale) {
                return null;
            }
            $pratica = $this->findOneBy('ClaimsHBundle:Pratica', array('ospedale' => $ospedale->getId(), 'anno' => $token[1], 'tpa' => $token[2]));
            return $pratica;
        }
        if ($claimant) {
            if ($claimant == self::CLAIMANT_TUTTI) {
                $claimant = array(self::CLAIMANT_CONTEC, self::CLAIMANT_RAVINALE);
            }
            $claimants = $claimant;
            foreach ($claimants as $claimant) {
                $nomi = $this->getRepository('ClaimsHBundle:Pratica')->nomi($claimant);
                $regexp = '/(' . implode('|', $nomi) . ')/i';
                $tpa = null;
                preg_match($regexp, $subject, $tpa);
                if (!$tpa) {
                    $txt = $body->getTxt();
                    preg_match($regexp, $txt, $tpa);
                }
                if (isset($tpa[0])) {
                    $schede = $this->findBy('ClaimsHBundle:Pratica', array('claimant' => $tpa[0]));
                    if (count($schede) == 1) {
                        return $schede[0];
                    }
                    return $schede;
                }
            }
        }
        return null;
    }

}

