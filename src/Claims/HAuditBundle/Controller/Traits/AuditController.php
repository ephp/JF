<?php

namespace Claims\HAuditBundle\Controller\Traits;

trait AuditController {

    private function sorting() {
        $dati = $this->getUser()->getDati();
        $sorting = $this->getParam('sorting', false);
        if (!$sorting && !isset($dati['claims_haudit_sorting'])) {
            $sorting = 'id';
        }
        if ($sorting) {
            $dati['claims_haudit_sorting'] = $sorting;
            $this->getUser()->setDati($dati);
            $this->persist($this->getUser());
        }
        if (isset($dati['claims_haudit_sorting'])) {
            $sorting = $dati['claims_haudit_sorting'];
        }
        $out = array();

        $out['id'] = array(
            'label' => 'Natural sort',
            'mode' => 'id',
        );
        if ($sorting == 'uid') {
            $out['uid']['icon'] = 'ico-chevron-up';
            $out['uid']['mode'] = 'iid';
        } elseif ($sorting == 'iid') {
            $out['uid']['icon'] = 'ico-chevron-down';
        }

        $out['claimant'] = array(
            'label' => 'Nome Claimant',
            'mode' => 'claimant',
        );
        if ($sorting == 'claimant') {
            $out['claimant']['icon'] = 'ico-chevron-up';
            $out['claimant']['mode'] = 'iclaimant';
        } elseif ($sorting == 'iclaimant') {
            $out['claimant']['icon'] = 'ico-chevron-down';
        }

        $out['reserve'] = array(
            'label' => 'Reserve',
            'mode' => 'reserve',
        );
        if ($sorting == 'reserve') {
            $out['reserve']['icon'] = 'ico-chevron-up';
            $out['reserve']['mode'] = 'ireserve';
        } elseif ($sorting == 'ireserve') {
            $out['reserve']['icon'] = 'ico-chevron-down';
        }

        return $out;
    }

    private function importBdx(\JF\ACLBundle\Entity\Cliente $cliente, $source, \Claims\HAuditBundle\Entity\Audit $audit) {
        $data = new SpreadsheetExcelReader($source, true, 'UTF-8');
        $pratiche = array();
        //return new \Symfony\Component\HttpFoundation\Response(json_encode($data->sheets));
        foreach ($data->sheets as $sheet) {
            $sheet = $sheet['cells'];
            $start = false;
            $colonne = array();
            foreach ($sheet as $riga => $valori_riga) {
                if (!$start) {
                    if (isset($valori_riga[1]) && in_array(strtoupper($valori_riga[1]), array('SRE'))) {
                        $colonne = $valori_riga;
                        $start = true;
                    }
                } else {
                    if (!isset($valori_riga[1]) || !$valori_riga[1]) {
                        continue;
                    } else {
                        try {
                            $this->getEm()->beginTransaction();
                            $pratica = new Pratica();
                            $pratica->setAudit($audit);
                            $pratica->setCliente($cliente);
                            $pratica->setPriorita($this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Nuovo')));
                            $pratica->setStatoPratica($this->findOneBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $cliente->getId(), 'primo' => true)));
                            foreach ($valori_riga as $idx => $value) {
                                if (!isset($colonne[$idx])) {
                                    continue;
                                }
                                switch (strtoupper($colonne[$idx])) {
                                    case 'TPA  REF.':
                                    case 'TPA REF.':
                                    case 'TPA REF':
                                        $pratica->setTpa($value);
                                        $pratica->setCodice($value);
                                        break;

                                    case 'TPA':
                                    case 'GROUP':
                                    case 'GRUPPO':
                                        $pratica->setGruppo($value);
                                        break;

                                    case 'MONTH':
                                        $pratica->setMese($value);
                                        break;

                                    case 'MFREF':
                                        $pratica->setMfRef($value);
                                        break;

                                    case 'HOSPITAL':
                                        $pratica->setOspedale($value);
                                        break;

                                    case 'YOA':
                                        $pratica->setAnno($value);
                                        break;

                                    case 'DS CODE':
                                        $pratica->setDsCode($value);
                                        break;

                                    case 'STATUS':
                                        $pratica->setStatus($value);
                                        break;

                                    case 'SRE':
                                        $pratica->setSre($value);
                                        break;

                                    case 'INDEMNITY + CTP PAID':
                                    case 'INDEMNITY+ CTP PAID':
                                    case 'INDEMNITY +CTP PAID':
                                    case 'INDEMNITY+CTP PAID':
                                        $pratica->setIndemnityCtpPaid(String::currency($value, ',', '.'));
                                        break;

                                    case 'CLAYMANT':
                                    case 'CLAIMANT':
                                        $pratica->setClaimant(utf8_encode($value));
                                        break;

                                    case 'DOL':
                                        if ($value) {
                                            $dol = \DateTime::createFromFormat('d/m/Y', $value);
                                            $pratica->setDol($dol);
                                        }
                                        break;
                                    case 'DON':
                                        if ($value) {
                                            $don = \DateTime::createFromFormat('d/m/Y', $value);
                                            $pratica->setDon($don);
                                        }
                                        break;
                                    case 'PAYMENTS':
                                    case 'PAYMENTS ':
                                        if ($value) {
                                            $pratica->setPayment(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'RESERVE':
                                        if ($value) {
                                            $pratica->setReserve(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    case 'PRO RESERVE':
                                        if ($value) {
                                            $pratica->setProReserve(String::currency($value, ',', '.'));
                                        }
                                        break;
                                    default: break;
                                }
                            }
                            $pratica->setDataImport(new \DateTime());
                            $pratica->addLog(array('Importata pratica'));
                            $this->persist($pratica);
                            $pratiche[] = $pratica;
                            $this->getEm()->commit();
                        } catch (\Exception $e) {
                            $this->getEm()->rollback();
                            throw $e;
                        }
                    }
                }
            }
        }

        return array('pratiche' => $pratiche);
    }

}
