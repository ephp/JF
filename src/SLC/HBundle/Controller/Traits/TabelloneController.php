<?php

namespace SLC\HBundle\Controller\Traits;

trait TabelloneController {

    protected function buildLinksSlc() {
        $out = array();

        $out['np'] = array(
            'label' => 'Analisi N.P.',
            'route' => 'slc_hospital_np',
        );
        $out['npsg'] = array(
            'label' => 'Analisi N.P. (senza gestore)',
            'route' => 'slc_hospital_np_sg',
        );
        $out['npcg'] = array(
            'label' => 'Analisi N.P. (con gestore)',
            'route' => 'slc_hospital_np_cg',
        );

        $out['riserve'] = array(
            'label' => 'Analisi Riserve',
            'route' => 'slc_hospital_riserve',
        );
        if ($this->getUser()->hasRole('R_SUPER')) {
            $out['bookeeping'] = array(
                'label' => 'Bookeeping',
                'route' => 'slc_hospital_bookeeping',
                'icon' => 'ico-money',
            );
        }
        return $out;
    }

    protected function sortingSlc() {
        $dati = $this->getUser()->getDati();
        $sorting = $this->getParam('sorting', false);
        if (!$sorting && !isset($dati['claims_h_sorting'])) {
            $sorting = 'anno';
        }
        if ($sorting) {
            $dati['claims_h_sorting'] = $sorting;
            $this->getUser()->setDati($dati);
            $this->persist($this->getUser());
        }
        if (isset($dati['claims_h_sorting'])) {
            $sorting = $dati['claims_h_sorting'];
        }
        $out = array();

        $out['anno'] = array(
            'label' => 'Anno e Ospedale',
            'mode' => 'anno',
        );
        if ($sorting == 'anno') {
            $out['anno']['icon'] = 'ico-chevron-up';
            $out['anno']['mode'] = 'ianno';
        } elseif ($sorting == 'ianno') {
            $out['anno']['icon'] = 'ico-chevron-down';
        }

        $out['soi'] = array(
            'label' => 'SOI',
            'mode' => 'soi',
        );
        if ($sorting == 'soi') {
            $out['soi']['icon'] = 'ico-chevron-up';
            $out['soi']['mode'] = 'isoi';
        } elseif ($sorting == 'isoi') {
            $out['soi']['icon'] = 'ico-chevron-down';
        }

        $out['dasc'] = array(
            'label' => 'DASC',
            'mode' => 'dasc',
        );
        if ($sorting == 'dasc') {
            $out['dasc']['icon'] = 'ico-chevron-up';
            $out['dasc']['mode'] = 'idasc';
        } elseif ($sorting == 'idasc') {
            $out['dasc']['icon'] = 'ico-chevron-down';
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

        $out['attivita'] = array(
            'label' => 'Attività',
            'mode' => 'attivita',
        );
        if ($sorting == 'attivita') {
            $out['attivita']['icon'] = 'ico-chevron-up';
            $out['attivita']['mode'] = 'iattivita';
        } elseif ($sorting == 'iattivita') {
            $out['attivita']['icon'] = 'ico-chevron-down';
        }

        return $out;
    }

    protected function buildFiltriSlc(&$tab) {
        $logger = $this->get('logger');
        $cliente = $this->getUser()->getCliente();
        $filtri = array(
            'in' => array(
                'cliente' => $cliente->getId(),
            ),
            'out' => array(
            ),
            'ricerca' => array(
            ),
        );
        $dati = $this->getUser()->getDati();
        switch ($tab) {
            // Legge in cache l'ultimo tipo di visualizzazione
            case 'default':
                $tab = isset($dati['slc_h_tab']) ? $dati['slc_h_tab'] : 'np';
                $logger->notice($tab);
                return $this->buildFiltriSlc($tab);
            // Vede solo 
            case 'np':
                $filtri['in']['amountReserved'] = -1;
                $filtri['sorting'] = '-firstReserveIndication';
                break;
            case 'npsg':
                $filtri['in']['amountReserved'] = -1;
                $filtri['in']['gestore'] = null;
                $filtri['sorting'] = '-firstReserveIndication';
                break;
            case 'npcg':
                $filtri['in']['amountReserved'] = -1;
                $filtri['out']['gestore'] = null;
                $filtri['sorting'] = '-firstReserveIndication';
                break;
            case 'riserve':
                $filtri['gt']['amountReserved'] = 0;
                $filtri['sorting'] = '-amountReserved';
                break;
            case 'bookeeping':
                $filtri['sorting'] = '-ltFeesReserve';
                break;
            default:
                break;
        }
        $datiCliente = $cliente->getDati();
        $analisi = $datiCliente['slc_h-analisi'];
        if (isset($analisi['sigle']) && $analisi['sigle']) {
            $sigle = explode(',', $analisi['sigle']);
            $ospedali = $this->findBy('ClaimsHBundle:Ospedale', array('sigla' => $sigle));
            $idOspedali = array();
            foreach ($ospedali as $ospedale) {
                $idOspedali[] = $ospedale->getId();
            }
            if (!$this->getUser()->hasRole('C_SUPVIS_H') && $this->getRequest()->get('hidden')) {
                $filtri['in']['ospedale'] = $idOspedali;
            } else {
                $filtri['out']['ospedale'] = $idOspedali;
            }
        }
        $dati['slc_h_tab'] = $tab;
        $this->getUser()->setDati($dati);
        $this->persist($this->getUser());
        return $filtri;
    }

    protected function getColonneSlc($mode, $view = 1) {
        $colonne = array();
                $colonne[] = 'index';
                $colonne[] = 'codice';
                $colonne[] = 'dasc';
                $colonne[] = 'giudiziale';
                $colonne[] = 'claimant';
                if (in_array($mode, array('np', 'npsg', 'npcg', 'riserve'))) {
                    $colonne[] = 'gestore';
                }
                $colonne[] = 'soi';
                if (in_array($mode, array('bookeeping'))) {
                    $colonne[] = 'ltFees';
                } else {
                    $colonne[] = 'amountReserved';
                    $colonne[] = 'firstReserve';
                }
                if ($this->hasRole('C_RECUPERI_H')) {
                    $colonne[] = 'datiRecupero';
                } else {
                    $colonne[] = 'note';
                }
                $colonne[] = 'stato';
                $colonne[] = 'operazioni';
        return $colonne;
    }

}
