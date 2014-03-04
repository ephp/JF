<?php

namespace Claims\HBundle\Controller\Traits;

trait ImportController {

    protected function salvaPratica(\JF\ACLBundle\Entity\Cliente $cliente, \Claims\HBundle\Entity\Pratica &$pratica, &$pratiche_aggiornate, &$pratiche_nuove, &$pratiche_invariate = false, $audit = false) {
        $old = $this->findOneBy('ClaimsHBundle:Pratica', array('cliente' => $cliente->getId(), 'codice' => $pratica->getCodice()));
//        \Ephp\UtilityBundle\Utility\Debug::pr($pratica->getCodice().' - '.($old ? $old->getCodice() : 'NUOVA'), true);
        /* @var $old \Claims\HBundle\Entity\Pratica */
        if ($old) {
            if ($audit) {
                $old->setInAudit(true);
                $this->persist($old);
            }
            $log = array();
            if ($old->getDol()->format('d-m-Y') != $pratica->getDol()->format('d-m-Y')) {
                $log[] = "DOL: da '" . $old->getDol()->format('d-m-Y') . "' a '" . $pratica->getDol()->format('d-m-Y') . "'";
                $old->setDol($pratica->getDol());
            }
            if ($old->getDon()->format('d-m-Y') != $pratica->getDon()->format('d-m-Y')) {
                $log[] = "DON: da '" . $old->getDon()->format('d-m-Y') . "' a '" . $pratica->getDon()->format('d-m-Y') . "'";
                $old->setDon($pratica->getDon());
            }
            if ($old->getTypeOfLoss() != $pratica->getTypeOfLoss()) {
                $log[] = "TYPE OF LOSS: da '" . $old->getTypeOfLoss(true) . "' a '" . $pratica->getTypeOfLoss(true) . "'";
                $old->setTypeOfLoss($pratica->getTypeOfLoss());
            }
            if ($old->getFirstReserveIndication() != $pratica->getFirstReserveIndication()) {
                $log[] = "FIRST RESERVE INDICATION: da '" . $old->getFirstReserveIndication() . "' a '" . $pratica->getFirstReserveIndication() . "'";
                $old->setFirstReserveIndication($pratica->getFirstReserveIndication());
            }
            if ($old->getApplicableDeductible() != $pratica->getApplicableDeductible()) {
                $log[] = "APPLICABLE DEDUCTIBLE: da '" . $old->getApplicableDeductible() . "' a '" . $pratica->getApplicableDeductible() . "'";
                $old->setApplicableDeductible($pratica->getApplicableDeductible());
            }
            $checkPriorita = true;
            if (($old->getAmountReserved() < 0 ? 'NP' : $old->getAmountReserved()) != ($pratica->getAmountReserved() < 0 ? 'NP' : $pratica->getAmountReserved())) {
                $_log = "AMOUNT RESERVED: da '" . ($old->getAmountReserved() < 0 ? 'NP' : $old->getAmountReserved()) . "' a '" . ($pratica->getAmountReserved() < 0 ? 'NP' : $pratica->getAmountReserved()) . "'";
                if (!$audit) {
                    if ($pratica->getAmountReserved() == 0) {
                        $evento = $this->newEvento($this->DEFINITO, $old, null, $_log);
                        $this->persist($evento);
                        if ($old->getPriorita()->getPriorita() != 'Chiuso') {
                            $old->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Pre-Chiusura')));
                            $checkPriorita = false;
                        }
                    } elseif ($pratica->getAmountReserved() < 0) {
                        $evento = $this->newEvento($this->RIPASSATONP, $old, null, $_log);
                        $this->persist($evento);
                        $old->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Ripassato NP')));
                        $checkPriorita = false;
                    } elseif ($old->getAmountReserved() < 0) {
                        $evento = $this->newEvento($this->RISERVA, $old, null, $_log);
                        $this->persist($evento);
                    }
                }
                $log[] = $_log;
                $old->setAmountReserved($pratica->getAmountReserved());
            }
            if ($old->getStatus() != $pratica->getStatus()) {
                if ($checkPriorita && $old->getPriorita() && $old->getPriorita()->getPriorita() == 'Chiuso') {
                    $log[] = "Pratica messa in priorità 'Riaperta'";
                    $old->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Riaperto')));
                }
                $old->setStatus($pratica->getStatus());
            }
            if (($old->getDeductibleReserved() < 0 ? 'NP' : $old->getDeductibleReserved()) != ($pratica->getDeductibleReserved() < 0 ? 'NP' : $pratica->getDeductibleReserved())) {
                $log[] = "DEDUCTIBLE RESERVED: da '" . ($old->getDeductibleReserved() < 0 ? 'NP' : $old->getDeductibleReserved()) . "' a '" . ($pratica->getDeductibleReserved() < 0 ? 'NP' : $pratica->getDeductibleReserved()) . "'";
                $old->setDeductibleReserved($pratica->getDeductibleReserved());
            }
            if ($old->getLtFeesReserve() != $pratica->getLtFeesReserve()) {
                $log[] = "LT FEES RESERVE: da '" . $old->getLtFeesReserve() . "' a '" . $pratica->getLtFeesReserve() . "'";
                $old->setLtFeesReserve($pratica->getLtFeesReserve());
            }
            if ($old->getProfessFeesReserve() != $pratica->getProfessFeesReserve()) {
                $log[] = "PROFESS. FEES RESERVE: da '" . $old->getProfessFeesReserve() . "' a '" . $pratica->getProfessFeesReserve() . "'";
                $old->setProfessFeesReserve($pratica->getProfessFeesReserve());
            }
            if ($old->getTpaFeesReserve() != $pratica->getTpaFeesReserve()) {
                $log[] = "TPA FEES RESERVE: da '" . $old->getTpaFeesReserve() . "' a '" . $pratica->getTpaFeesReserve() . "'";
                $old->setTpaFeesReserve($pratica->getTpaFeesReserve());
            }
            if ($old->getPossibleRecovery() != $pratica->getPossibleRecovery()) {
                $log[] = "POSSIBLE RECOVERY: da '" . $old->getPossibleRecovery() . "' a '" . $pratica->getPossibleRecovery() . "'";
                $old->setPossibleRecovery($pratica->getPossibleRecovery());
            }
            if ($old->getAmountSettled() != $pratica->getAmountSettled()) {
                $log[] = "AMOUNT SETTLED: da '" . $old->getAmountSettled() . "' a '" . $pratica->getAmountSettled() . "'";
                $old->setAmountSettled($pratica->getAmountSettled());
            }
            if ($old->getDeducPaid() != $pratica->getDeducPaid()) {
                $log[] = "DEDUC. PAID: da '" . $old->getDeducPaid() . "' a '" . $pratica->getDeducPaid() . "'";
                $old->setDeducPaid($pratica->getDeducPaid());
            }
            if ($old->getLtFeesPaid() != $pratica->getLtFeesPaid()) {
                $log[] = "LT FEES PAID: da '" . $old->getLtFeesPaid() . "' a '" . $pratica->getLtFeesPaid() . "'";
                $old->setLtFeesPaid($pratica->getLtFeesPaid());
            }
            if ($old->getProfessFeesPaid() != $pratica->getProfessFeesPaid()) {
                $log[] = "PROFESS. FEES PAID: da '" . $old->getProfessFeesPaid() . "' a '" . $pratica->getProfessFeesPaid() . "'";
                $old->setProfessFeesPaid($pratica->getProfessFeesPaid());
            }
            if ($old->getTpaFeesPaid() != $pratica->getTpaFeesPaid()) {
                $log[] = "TPA FEES PAID: da '" . $old->getTpaFeesPaid() . "' a '" . $pratica->getTpaFeesPaid() . "'";
                $old->setTpaFeesPaid($pratica->getTpaFeesPaid());
            }
            if ($old->getTotalPaid() != $pratica->getTotalPaid()) {
                $log[] = "TOTAL PAID: da '" . $old->getTotalPaid() . "' a '" . $pratica->getTotalPaid() . "'";
                $old->setTotalPaid($pratica->getTotalPaid());
            }
            if ($old->getRecovered() != $pratica->getRecovered()) {
                $log[] = "RECOVERED: da '" . $old->getRecovered() . "' a '" . $pratica->getRecovered() . "'";
                $old->setRecovered($pratica->getRecovered());
            }
            if ($old->getTotalIncurred() != $pratica->getTotalIncurred()) {
                $log[] = "TOTAL INCURRED: da '" . $old->getTotalIncurred() . "' a '" . $pratica->getTotalIncurred() . "'";
                $old->setTotalIncurred($pratica->getTotalIncurred());
            }
            if ($old->getSp() != $pratica->getSp()) {
                $log[] = "S.P.: da '" . $old->getSp(true) . "' a '" . $pratica->getSp(true) . "'";
                $old->setSp($pratica->getSp());
            }
            if ($old->getMpl() != $pratica->getMpl()) {
                $log[] = "M.P.L.: da '" . $old->getMpl(true) . "' a '" . $pratica->getMpl(true) . "'";
                $old->setMpl($pratica->getMpl());
            }
            if ($old->getSoi() != $pratica->getSoi()) {
                $log[] = "S. OF I.: da '" . $old->getSoi(true) . "' a '" . $pratica->getSoi(true) . "'";
                $old->setSoi($pratica->getSoi());
            }
            if ($old->getAll() != $pratica->getAll()) {
                $log[] = "ALL: da '" . $old->getAll(true) . "' a '" . $pratica->getAll(true) . "'";
                $old->setAll($pratica->getAll());
            }
            if ($old->getCourt() != $pratica->getCourt()) {
                $log[] = "COURT: da '" . $old->getCourt() . "' a '" . $pratica->getCourt() . "'";
                $old->setCourt($pratica->getCourt());
            }
            if ($old->getReq() != $pratica->getReq()) {
                $log[] = "REQ: da '" . $old->getReq() . "' a '" . $pratica->getReq() . "'";
                $old->setReq($pratica->getReq());
            }
            if ($old->getOthPol() != $pratica->getOthPol()) {
                $log[] = "OTHER POLICIES: da '" . $old->getOthPol() . "' a '" . $pratica->getOthPol() . "'";
                $old->setOthPol($pratica->getOthPol());
            }
            if ($old->getStatus() != $pratica->getStatus()) {
                $log[] = "STATUS: da '" . $old->getStatus() . "' a '" . $pratica->getStatus() . "'";
                $old->setStatus($pratica->getStatus());
            }
            if ($old->getRo() != $pratica->getRo()) {
                $log[] = "RO: da '" . $old->getRo() . "' a '" . $pratica->getRo() . "'";
                $old->setRo($pratica->getRo());
            }
            if (is_null($old->getMedicalExaminer()) != is_null($pratica->getMedicalExaminer()) || (!is_null($old->getMedicalExaminer()) && $old->getMedicalExaminer()->format('d-m-Y') != $pratica->getMedicalExaminer()->format('d-m-Y'))) {
                $log[] = "MEDIACAL EXAMINER : da '" . ($old->getMedicalExaminer() ? $old->getMedicalExaminer()->format('d-m-Y') : 'Non impostata') . "' a '" . ($pratica->getMedicalExaminer() ? $pratica->getMedicalExaminer()->format('d-m-Y') : 'Non impostata') . "'";
                $old->setMedicalExaminer($pratica->getMedicalExaminer());
            }
            if (is_null($old->getLegalTeam()) != is_null($pratica->getLegalTeam()) || (!is_null($old->getLegalTeam()) && $old->getLegalTeam()->format('d-m-Y') != $pratica->getLegalTeam()->format('d-m-Y'))) {
                $log[] = "LEGAL TEAM : da '" . ($old->getLegalTeam() ? $old->getLegalTeam()->format('d-m-Y') : 'Non impostata') . "' a '" . ($pratica->getLegalTeam() ? $pratica->getLegalTeam()->format('d-m-Y') : 'Non impostata') . "'";
                $old->setLegalTeam($pratica->getLegalTeam());
                $old->setDasc($pratica->getLegalTeam());
            }
            if ($old->getComments() != $pratica->getComments()) {
                $log[] = "COMMENTS: da '" . $old->getComments() . "' a '" . $pratica->getComments() . "'";
                $old->setComments($pratica->getComments());
            }
            if ($audit || count($log) > 0) {
//                \Ephp\UtilityBundle\Utility\Debug::pr($log, true);
                $old->addLog($log);
                if (!$audit) {
                    $this->persist($old);
                    $evento = $this->newEvento($this->BORDERAUX, $old, null, implode("\n", $log));
                    $this->persist($evento);
                }
                $pratiche_aggiornate[] = $old;
            } else {
                $pratiche_invariate[] = $old;
            }
        } else {
//            \Ephp\UtilityBundle\Utility\Debug::pr(array('Importata pratica'), true);
            if (!$audit) {
                $pratica->setDataImport(new \DateTime());
                $pratica->addLog(array('Importata pratica'));
                $this->persist($pratica);
            }
            $pratiche_nuove[] = $pratica;
        }
    }

}
