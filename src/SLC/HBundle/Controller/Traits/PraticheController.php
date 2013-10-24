<?php

namespace SLC\HBundle\Controller\Traits;

trait PraticheController {
    
    protected static $TPA = "/[a-z]{2}[a-z0-9]{1}( [a-z.]+[a-z]{1})?(\/|\-)[0-9]{2}\/[0-9]{1,3}/i";
    protected static $CLAIMANT_CONTEC = "Contec";
    protected static $CLAIMANT_RAVINALE = "Ravinale";
    protected static $CLAIMANT_TUTTI = 'ALL';

    private function findPratica(\Ephp\ImapBundle\Entity\Body $body, $claimant = null) {
        $subject = $body->getSubject();
        $tpa = null;
        preg_match(self::$TPA, $subject, $tpa);
        if (!$tpa) {
            $txt = $body->getTxt();
            preg_match(self::$TPA, $txt, $tpa);
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
            if ($claimant == self::$CLAIMANT_TUTTI) {
                $claimant = array(self::$CLAIMANT_CONTEC, self::$CLAIMANT_RAVINALE);
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
