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


}
