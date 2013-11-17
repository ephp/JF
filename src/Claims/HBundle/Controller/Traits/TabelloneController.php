<?php

namespace Claims\HBundle\Controller\Traits;

trait TabelloneController {

    private function buildLinks($full = true) {
        $out = array();
        if ($full) {
            if ($this->getUser()->hasRole(array('C_GESTORE_H', 'C_RECUPERI_H'))) {
                $out['personale'] = array(
                    'route' => 'claims_hospital_personale',
                    'label' => 'Personale',
                    'icon' => 'ico-user',
                );
                $out['chiuso'] = array(
                    'route' => 'claims_hospital_chiuso',
                    'label' => 'Chiusi',
                    'icon' => 'ico-user',
                );
                $out['tutti'] = array(
                    'route' => 'claims_hospital_tutti',
                    'label' => 'Tutti personali',
                    'icon' => 'ico-user',
                );
            }
            if ($this->getUser()->hasRole(array('C_RECUPERI_H'))) {
                $out['recupero'] = array(
                    'route' => 'claims_hospital_recupero',
                    'label' => 'In recupero',
                    'icon' => 'ico-money-bag',
                );
                $out['recuperati'] = array(
                    'route' => 'claims_hospital_recuperati',
                    'label' => 'Recuperati',
                    'icon' => 'ico-money-bag',
                );
            }
            $out['aperti'] = array(
                'route' => 'claims_hospital_aperti',
                'label' => 'Aperti',
                'icon' => 'ico-group',
            );
            if ($this->getUser()->hasRole(array('C_ADMIN', 'C_RECUPERI_H'))) {
                $out['chiusi'] = array(
                    'route' => 'claims_hospital_chiusi',
                    'label' => 'Tutti i chiusi',
                    'icon' => 'ico-group',
                );
                $out['completo'] = array(
                    'route' => 'claims_hospital_completo',
                    'label' => 'Completo',
                    'icon' => 'ico-group',
                );
            }
            if ($this->getUser()->hasRole('C_ADMIN')) {
                $out['senza_dasc'] = array(
                    'route' => 'claims_hospital_senza_dasc',
                    'label' => 'Senza DASC',
                    'icon' => 'ico-tools',
                );
                $out['senza_gestore'] = array(
                    'route' => 'claims_hospital_senza_gestore',
                    'label' => 'Senza gestore',
                    'icon' => 'ico-tools',
                );
            }
        } else {
            if ($this->getUser()->hasRole(array('C_GESTORE_H', 'C_RECUPERI_H'))) {
                $out['personale'] = array(
                    'route' => 'claims_stati_hospital_personale',
                    'label' => 'Personale',
                    'icon' => 'ico-user',
                );
            }
            if ($this->getUser()->hasRole('C_ADMIN', 'C_RECUPERI_H')) {
                $out['completo'] = array(
                    'route' => 'claims_stati_hospital_completo',
                    'label' => 'Completo',
                    'icon' => 'ico-group',
                );
            }
            if ($this->getUser()->getCliente()->hasLicenza('slc.h-analisi', 'slc')) {
                $out['xls'] = array(
                    'route' => $this->getParam('_route') . '_all_xls',
                    'label' => 'Esporta XLS',
                    'icon' => 'ico-table',
                    'class' => 'label-important',
                );
            }
        }
        $out['search'] = array(
            'fancybox' => 'fb_ricerca',
            'label' => 'Ricerca',
            'class' => $this->getParam('ricerca') ? 'label-success' : 'label-info',
            'icon' => 'ico-search'
        );
//        $out['stampa'] = array(
//            'route' => $this->getParam('_route') . '_stampa',
//            'label' => 'Versione per la stampa',
//            'icon' => 'ico-printer',
//            'class' => 'label-warning',
//            'target' => '_blank'
//        );
//        if ($this->getParam('ricerca')) {
//            $out['stampa']['params'] = array('ricerca' => $this->getParam('ricerca'));
//        }
        if ($this->getUser()->getCliente()->hasLicenza('cl.h-pratiche', 'slc') && $this->getUser()->hasRole('C_ADMIN')) {
            $out['monthly_report'] = array(
                'route' => $this->getParam('_route') . '_stampa',
                'params' => array('monthly_report' => 'monthly-report'),
                'label' => 'Stampa monthly report',
                'icon' => 'ico-printer',
                'class' => 'label-warning',
                'target' => '_blank'
            );
            if ($this->getParam('monthly_report')) {
                $out['monthly_report']['params'] = array('monthly_report' => 'monthly-report', 'ricerca' => $this->getParam('ricerca'));
            }
        }
        return $out;
    }

    private function sorting() {
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

        $out['importazione'] = array(
            'label' => 'Data di importazione',
            'mode' => 'importazione',
        );
        if ($sorting == 'importazione') {
            $out['importazione']['icon'] = 'ico-chevron-up';
            $out['importazione']['mode'] = 'iimportazione';
        } elseif ($sorting == 'iattivita') {
            $out['importazione']['icon'] = 'ico-chevron-down';
        }

        return $out;
    }

    private function buildFiltri(&$mode, &$stato = null) {
        $logger = $this->get('logger');
        $cliente = $this->getUser()->getCliente();
        /* @var $cliente \JF\ACLBundle\Entity\Cliente */
        $filtri = array(
            'in' => array(
                'cliente' => $cliente->getId(),
            ),
            'out' => array(
            ),
        );
        if ($this->getUser()->get('claims_h_sistema') != 'tutti') {
            $filtri['in']['sistema'] = $this->getUser()->get('claims_h_sistema');
        }
        $dati = $this->getUser()->getDati();
        if ($stato) {
            if (!in_array($mode, array("default", "completo", "personale"))) {
                $mode = 'default';
                unset($dati['claims_h']);
            }
            if ($stato == 'default') {
                if (isset($dati['claims_h_stato'])) {
                    $stato = $dati['claims_h_stato'];
                } else {
                    $stato = $this->findOneBy('ClaimsCoreBundle:StatoPratica', array('cliente' => $this->getUser()->getCliente()->getId(), 'tab' => true))->getId();
                }
            }
        }
        switch ($mode) {
            // Legge in cache l'ultimo tipo di visualizzazione
            case 'default':
                $set_default = false;
                if ($this->getUser()->hasRole(array('C_GESTORE_H', 'C_RECUPERI_H'))) {
                    if (!isset($dati['claims_h']) || (!$this->getUser()->hasRole(array('C_ADMIN')) && in_array($dati['claims_h'], array('senza_dasc', 'senza_gestore', 'chiusi')))) {
                        $set_default = true;
                    }
                    $default = 'personale';
                } elseif ($this->getUser()->hasRole(array('C_ADMIN'))) {
                    if (!isset($dati['claims_h']) || (!$this->getUser()->hasRole(array('C_GESTORE_H')) && in_array($dati['claims_h'], array('personale', 'chiuso')))) {
                        $set_default = true;
                    }
                    $default = 'completo';
                }
                $mode = $set_default ? $default : $dati['claims_h'];
                $logger->notice($mode);
                return $this->buildFiltri($mode, $stato);
            // Gestore e Recuperi
            case 'recupero':
                $filtri['in']['recupero'] = true;
                break;
            case 'recuperati':
                $filtri['in']['recuperati'] = true;
                break;
            case 'tutti':
                if ($this->getUser()->hasRole('C_RECUPERI_H')) {
                    $filtri['in']['recuperi'] = $this->getUser()->getId();
                } else {
                    $filtri['in']['gestore'] = $this->getUser()->getId();
                }
                break;
            case 'personale':
                if ($this->getUser()->hasRole('C_RECUPERI_H')) {
                    $filtri['in']['recuperi'] = $this->getUser()->getId();
                } else {
                    $filtri['in']['gestore'] = $this->getUser()->getId();
                }
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                $filtri['out']['dasc'] = null;
                break;
            case 'chiuso':
                if ($this->getUser()->hasRole('C_RECUPERI_H')) {
                    $filtri['in']['recuperi'] = $this->getUser()->getId();
                } else {
                    $filtri['in']['gestore'] = $this->getUser()->getId();
                }
                $filtri['in']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                break;

            // Amministratore e Recuperi
            case 'aperti':
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                $filtri['out']['dasc'] = null;
                $filtri['out']['gestore'] = null;
                break;
            case 'completo':
                break;
            case 'chiusi':
                $filtri['in']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                break;

            // Amministratore
            case 'senza_dasc':
                $filtri['in']['dasc'] = null;
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                break;
            case 'senza_gestore':
                $filtri['in']['gestore'] = null;
                $filtri['out']['priorita'] = $this->findOneBy('ClaimsCoreBundle:Priorita', array('priorita' => 'Chiuso'));
                $filtri['out']['dasc'] = null;
                break;
            default:
                break;
        }
        $datiCliente = $cliente->getDati();
        if (isset($datiCliente['slc_h-analisi'])) {
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
        }
        if ($stato) {
            $dati['claims_h_stato'] = $stato;
            $filtri['in']['statoPratica'] = $stato;
        }
        $filtri['ricerca'] = $this->getParam('ricerca', array());
        $filtri['sorting'] = $dati['claims_h_sorting'];
        $dati['claims_h'] = $mode;
        $this->getUser()->setDati($dati);
        $this->persist($this->getUser());
        return $filtri;
    }

    protected $V_STANDARD = 1;
    protected $V_MONTLY_REPORT = 2;
    protected $V_AUDIT = 3;

    protected function getColonne($mode, $view = 1) {
        $colonne = array();
        switch ($view) {
            case $this->V_STANDARD:
                if (in_array($mode, array('personale', 'chiuso', 'tutti', 'senza_dasc', 'senza_gestore', 'cerca'))) {
                    $colonne[] = 'index';
                }
                $colonne[] = 'codice';
                $colonne[] = 'dasc';
                $colonne[] = 'giudiziale';
                $colonne[] = 'claimant';
                if (in_array($mode, array('aperti', 'completo', 'chiusi', 'senza_dasc', 'senza_gestore', 'cerca', 'np', 'npcg'))) {
                    $colonne[] = 'gestore';
                }
                $colonne[] = 'soi';
                if (in_array($mode, array('bookeeping'))) {
                    $colonne[] = 'ltFees';
                } else {
                    if (in_array($mode, array('recupero', 'recuperati'))) {
                        $colonne[] = 'offerte';
                    } else {
                        $colonne[] = 'amountReserved';
                        if (in_array($mode, array('np', 'npcg', 'npsg', 'reserve'))) {
                            $colonne[] = 'firstReserve';
                        }
                    }
                }
                if ($this->hasRole('C_RECUPERI_H')) {
                    $colonne[] = 'datiRecupero';
                } else {
                    $colonne[] = 'note';
                }
                $colonne[] = 'stato';
                $colonne[] = 'operazioni';
                break;
            case $this->V_MONTLY_REPORT:
                $colonne[] = 'index';
                $colonne[] = 'codice';
                $colonne[] = 'claimant';
                if (in_array($mode, array('completo', 'senza_gestore'))) {
                    $colonne[] = 'gestore';
                }
                $colonne[] = 'soi';
                $colonne[] = 'amountReserved';
                $colonne[] = 'note';
                $colonne[] = 'monthly';
                $colonne[] = 'stato';
                $colonne[] = 'operazioni';
                break;
            case $this->V_AUDIT:
                $colonne[] = 'index';
                $colonne[] = 'codice';
                $colonne[] = 'claimant';
                if (in_array($mode, array('completo', 'senza_gestore'))) {
                    $colonne[] = 'gestore';
                }
                $colonne[] = 'amountReserved';
                $colonne[] = 'note';
                $colonne[] = 'audit';
                $colonne[] = 'percentuale';
                $colonne[] = 'azioni';
                $colonne[] = 'stato';
                $colonne[] = 'operazioni';
                break;
        }
        return $colonne;
    }

    public function getSistemi() {
        $sistema = $this->getParam('sistema', $this->getUser()->get('claims_h_sistema', 'tutti'));
        if ($sistema != $this->getUser()->get('claims_h_sistema')) {
            $this->getUser()->set('claims_h_sistema', $sistema);
            $this->persist($this->getUser());
        }
        $sistemi = array('tutti' => 'Tutti');
        foreach ($this->findAll('ClaimsHBundle:Sistema') as $sistema) {
            /* @var $sistema \Claims\HBundle\Entity\Sistema */
            $sistemi[strtolower($sistema->getNome())] = $sistema->getNome();
        }
        return $sistemi;
    }

}
