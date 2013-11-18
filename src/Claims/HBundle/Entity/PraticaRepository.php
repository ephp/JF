<?php

namespace Claims\HBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PraticaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PraticaRepository extends EntityRepository {

    public function filtra($filtri = array()) {
        if (!isset($filtri['in'])) {
            $filtri['in'] = array();
        }
        if (!isset($filtri['out'])) {
            $filtri['out'] = array();
        }
        if (!isset($filtri['lt'])) {
            $filtri['lt'] = array();
        }
        if (!isset($filtri['gt'])) {
            $filtri['gt'] = array();
        }
        if (!isset($filtri['bt'])) {
            $filtri['bt'] = array();
        }
        if (!isset($filtri['sorting'])) {
            $filtri['sorting'] = 'anno';
        }
        $q = $this->createQueryBuilder('p');
        if (!isset($filtri['ricerca']) || count($filtri['ricerca']) == 0) {
            foreach ($filtri['in'] as $field => $value) {
                if (is_null($value)) {
                    $q->andWhere("p.{$field} IS NULL");
                } else {
                    switch ($field) {
                        case 'recuperi':
                            $q->andWhere("p.recupero = :true OR p.gestore = :gestore")
                                    ->setParameter('true', true)
                                    ->setParameter('gestore', $value);
                            break;
                        case 'recuperati':
                            $q->andWhere("p.datiRecupero IS NOT NULL OR p.recuperoResponsabile IS NOT NULL OR p.recuperoSollecitoAsl IS NOT NULL OR p.recuperoCopiaPolizza IS NOT NULL OR p.recuperoEmailLiquidatore IS NOT NULL OR p.recuperoQuietanze IS NOT NULL OR p.recuperoOffertaNostra IS NOT NULL OR p.recuperoOffertaLoro IS NOT NULL OR (p.recuperoAzioneDiRecupero IS NOT NULL AND p.recuperoAzioneDiRecupero != :empty)")
                                    ->andWhere("p.recupero = :false OR p.recupero IS NULL")
                                    ->setParameter('empty', '')
                                    ->setParameter('false', false);
                            break;
                        case 'q':
                            /* @var $value \DateTime */
                            $q->leftJoin('p.eventi', 'e')
                                    ->andWhere("
                                        p.claimant LIKE :{$field}
                                     OR p.note LIKE :{$field}
                                     OR p.datiRecupero LIKE :{$field}
                                     OR e.titolo LIKE :{$field}
                                     OR e.note LIKE :{$field}")
                                    ->setParameter($field, "%{$value}%");
                            break;
                        case 'evento':
                            /* @var $value \DateTime */
                            $da = \DateTime::createFromFormat('d-m-Y', $value->format('d-m-Y'));
                            $a = \DateTime::createFromFormat('d-m-Y', $value->format('d-m-Y'));
                            $da->setTime(0, 0, 0);
                            $a->setTime(23, 59, 59);
                            $q->leftJoin('p.eventi', 'e');
                            $q->andWhere("e.data_ora BETWEEN :da AND :a")
                                    ->setParameter('da', $da)
                                    ->setParameter('a', $a);
                            break;
                        case 'sistema':
                            $q->leftJoin('p.ospedale', 'h');
                            $q->leftJoin('h.sistema', 's');
                            $q->andWhere("s.nome = :sistema")
                                    ->setParameter('sistema', $value);
                            break;
                        case 'evento_recupero':
                            $q->leftJoin('e.tipo', 't');
                            $q->andWhere("p.gestore = :gestore OR t.sigla = :sigla")
                                    ->setParameter('gestore', $value)
                                    ->setParameter('sigla', 'RCM');
                            break;
                        case 'claimant':
                            $q->andWhere("p.{$field} LIKE :{$field}")
                                    ->setParameter($field, "%{$value}%");
                            break;
                        case 'dasc':
                        case 'dol':
                        case 'don':
                        case 'medical_examiner':
                        case 'legal_team':
                            $q->andWhere("p.{$field} = :{$field}")
                                    ->setParameter($field, \DateTime::createFromFormat('d-m-Y', $value));
                            break;
                        default:
                            if (is_array($value)) {
                                if (count($value) == 0) {
                                    $value[] = 0;
                                }
                                $q->andWhere($q->expr()->in("p.{$field}", $value));
                            } else {
                                $q->andWhere("p.{$field} = :{$field}")
                                        ->setParameter($field, $value);
                            }
                            break;
                    }
                }
            }
            foreach ($filtri['out'] as $field => $value) {
                if (is_null($value)) {
                    $q->andWhere("p.{$field} IS NOT NULL");
                } else {
                    switch ($field) {
                        case 'claimant':
                            $q->andWhere("p.{$field} NOT LIKE :{$field}")
                                    ->setParameter($field, "%{$value}%");
                            break;
                        case 'dasc':
                        case 'dol':
                        case 'don':
                        case 'medical_examiner':
                        case 'legal_team':
                            $q->andWhere("p.{$field} != :{$field}")
                                    ->setParameter($field, \DateTime::createFromFormat('d-m-Y', $value));
                            break;
                        default:
                            if (is_array($value)) {
                                if (count($value) == 0) {
                                    $value[] = 0;
                                }
                                $q->andWhere($q->expr()->notIn("p.{$field}", $value));
                            } else {
                                $q->andWhere("p.{$field} != :{$field}")
                                        ->setParameter($field, $value);
                            }
                            break;
                    }
                }
            }
            foreach ($filtri['gt'] as $field => $value) {
                switch ($field) {
                    case 'dasc':
                    case 'dol':
                    case 'don':
                    case 'medical_examiner':
                    case 'legal_team':
                        $q->andWhere("p.{$field} >= :{$field}")
                                ->setParameter($field, \DateTime::createFromFormat('d-m-Y', $value));
                        break;
                    default:
                        $q->andWhere("p.{$field} >= :{$field}")
                                ->setParameter($field, $value);
                        break;
                }
            }
            foreach ($filtri['lt'] as $field => $value) {
                switch ($field) {
                    case 'dasc':
                    case 'dol':
                    case 'don':
                    case 'medical_examiner':
                    case 'legal_team':
                        $q->andWhere("p.{$field} <= :{$field}")
                                ->setParameter($field, \DateTime::createFromFormat('d-m-Y', $value));
                        break;
                    default:
                        $q->andWhere("p.{$field} <= :{$field}")
                                ->setParameter($field, $value);
                        break;
                }
            }
            foreach ($filtri['bt'] as $field => $value) {
                switch ($field) {
                    case 'dasc':
                    case 'dol':
                    case 'don':
                    case 'medical_examiner':
                    case 'legal_team':
                        $q->andWhere("p.{$field} BETWEEN :{$field}_from AND {$field}_to")
                                ->setParameter($field . '_from', \DateTime::createFromFormat('d-m-Y', $value[0]))
                                ->setParameter($field . '_to', \DateTime::createFromFormat('d-m-Y', $value[1]));
                        break;
                    default:
                        $q->andWhere("p.{$field} BETWEEN :{$field}_from AND {$field}_to")
                                ->setParameter($field . '_from', $value[0])
                                ->setParameter($field . '_to', $value[1]);
                        break;
                }
            }
        } else {
            foreach ($filtri['in'] as $field => $value) {
                switch ($field) {
                    case 'cliente':
                        if (is_array($value)) {
                            if (count($value) == 0) {
                                $value[] = 0;
                            }
                            $q->andWhere($q->expr()->in("p.{$field}", $value));
                        } else {
                            $q->andWhere("p.{$field} = :{$field}")
                                    ->setParameter($field, $value);
                        }
                        break;
                    default:
                        break;
                }
            }
            foreach ($filtri['ricerca'] as $field => $value) {
                if ($value) {
                    switch ($field) {
                        case 'submit':
                        case '_token':
                            break;
                        case 'claimant':
                        case 'codice':
                        case 'status':
                            $q->andWhere("p.{$field} LIKE :{$field}")
                                    ->setParameter($field, "%{$value}%");
                            break;
                        case 'amountReserved':
                            if ($value == 'N.P.') {
                                $q->andWhere("p.amountReserved < :{$field}")
                                        ->setParameter($field, 0);
                            } else {
                                $q->andWhere("p.amountReserved >= :{$field}")
                                        ->setParameter($field, 0);
                            }
                            break;
                        case 'court':
                            if ($value == 'Sì') {
                                $q->andWhere("p.court != :{$field}")
                                        ->setParameter($field, '');
                            } else {
                                $q->andWhere("p.court = :{$field}")
                                        ->setParameter($field, '');
                            }
                            break;
                        default:
                            $q->andWhere("p.{$field} = :{$field}")
                                    ->setParameter($field, $value);
                            break;
                    }
                }
            }
        }
        switch ($filtri['sorting']) {
            case 'anno':
                $q->leftJoin('p.ospedale', 'o');
                $q->orderBy('p.anno', 'asc');
                $q->addOrderBy('o.sigla', 'asc');
                break;
            case 'ianno':
                $q->leftJoin('p.ospedale', 'o');
                $q->orderBy('p.anno', 'desc');
                $q->addOrderBy('o.sigla', 'desc');
                break;
            case 'soi':
                $q->orderBy('p.soi', 'asc');
                $q->addOrderBy('p.amountReserved', 'desc');
                break;
            case 'isoi':
                $q->orderBy('p.soi', 'desc');
                $q->addOrderBy('p.amountReserved', 'desc');
                break;
            case 'dasc':
                $q->orderBy('p.dasc', 'asc');
                break;
            case 'idasc':
                $q->orderBy('p.dasc', 'desc');
                break;
            case 'claimant':
                $q->orderBy('p.claimant', 'asc');
                break;
            case 'iclaimant':
                $q->orderBy('p.claimant', 'desc');
                break;
            case 'attivita':
                $q->leftJoin('p.eventi', 'e');
                $q->orderBy('e.data_ora', 'asc');
                break;
            case 'iattivita':
                $q->leftJoin('p.eventi', 'e');
                $q->orderBy('e.data_ora', 'desc');
                break;
            case 'importazione':
                $q->orderBy('p.dataImport', 'desc');
                $q->addOrderBy('p.id', 'desc');
                break;
            case 'iimportazione':
                $q->orderBy('p.dataImport', 'asc');
                $q->addOrderBy('p.id', 'desc');
                break;
            default:
                if ($filtri['sorting']{0} == '-') {
                    $q->orderBy('p.' . substr($filtri['sorting'], 1), 'desc');
                } else {
                    $q->orderBy('p.' . $filtri['sorting'], 'asc');
                }
                break;
        }
        return $q;
    }

    public function ritardi($cliente_id, $gestore_id = null) {
        $connection = $this->getEntityManager()->getConnection();
        $q = "   
SELECT * FROM claims_h_ritardi r 
 WHERE r.cliente_id = :cliente
   AND r.priorita != :priorita 
   AND r.giorni > :giorni 
";
        $params = array(
            'cliente' => $cliente_id,
            'priorita' => 'Chiuso',
            'giorni' => 60,
        );

        if ($gestore_id) {
            $q .= "
   AND r.gestore_id = :id 
";
            $params['id'] = $gestore_id;
        }
        $stmt = $connection->executeQuery($q, $params);
        $out = $stmt->fetchAll();
        foreach ($out as $i => $row) {
            $out[$i]['entity'] = $this->find($row['id']);
        }
        return $out;
    }

    public function nomi($nome) {
        $connection = $this->getEntityManager()->getConnection();
        $q = "
SELECT s.claimant
  FROM claims_h_pratiche s
        ";
        $params = array(
        );
        if (is_string($nome)) {
            $q .= "
 INNER JOIN claims_h_ospedali o ON o.id = s.ospedale_id
 INNER JOIN claims_h_sistemi t ON t.id = s.sistema_id
 WHERE t.nome = :nome
        ";
            $params['nome'] = $nome;
        }

        $stmt = $connection->executeQuery($q, $params);
        $rows = $stmt->fetchAll();
        $out = array();
        foreach ($rows as $row) {
            $out[] = trim(str_replace(array('  ', '+'), array(' ', ''), $row['claimant']));
        }
        return array_unique($out);
    }

    public function cancellaMR(\JF\ACLBundle\Entity\Cliente $cliente) {
        $connection = $this->getEntityManager()->getConnection();
        $q = "   
UPDATE claims_h_pratiche p 
   SET p.in_monthly_report = :false
 WHERE p.cliente_id = :cliente
";
        $params = array(
            'false' => false,
            'cliente' => $cliente->getId(),
        );

        $connection->executeUpdate($q, $params);
    }

    public function cancellaAudit(\JF\ACLBundle\Entity\Cliente $cliente) {
        $connection = $this->getEntityManager()->getConnection();
        $q = "   
UPDATE claims_h_pratiche p 
   SET p.in_audit = :false
 WHERE p.cliente_id = :cliente
";
        $params = array(
            'false' => false,
            'cliente' => $cliente->getId(),
        );

        $connection->executeUpdate($q, $params);
    }

    public function cercaDaMR(\JF\ACLBundle\Entity\Cliente $cliente, $claimant, $ospedale, $anno) {
        $anno = intval($anno) - 2000;
        $claimant = trim($claimant);
        $connection = $this->getEntityManager()->getConnection();
        $q = "   
SELECT id FROM claims_h_pratiche p 
 WHERE (:claimant like CONCAT(REPLACE(p.claimant, ' ', '% '), '%') OR p.claimant like :claimant2)
   AND p.ospedale_id IN ( SELECT o.id
                            FROM claims_h_ospedali o
                           WHERE o.ospedale LIKE :ospedale ) 
   AND p.anno = :anno 
   AND p.cliente_id = :cliente
";
        $params = array(
            'claimant' => $claimant,
            'claimant2' => str_replace(array(' ', "'", '%%'), array('% ', '%', '%'), $claimant) . '%',
            'ospedale' => str_replace(array(' ', ".", '%%'), array('%', '%', '%'), $ospedale),
            'anno' => $anno,
            'cliente' => $cliente->getId(),
        );

        $stmt = $connection->executeQuery($q, $params);

        $rows = $stmt->fetchAll();
        if (count($rows) == 0) {
            $q = "   
    SELECT id FROM claims_h_pratiche p 
     WHERE (:claimant like CONCAT(REPLACE(p.claimant, ' ', '% '), '%') OR p.claimant like :claimant2)
       AND p.ospedale_id IN ( SELECT o.id
                                FROM claims_h_ospedali o
                               WHERE o.ospedale LIKE :ospedale ) 
       AND p.cliente_id = :cliente
    ";
            $params = array(
                'claimant' => $claimant,
                'claimant2' => str_replace(array(' ', "'", '%%'), array('% ', '%', '%'), $claimant) . '%',
                'ospedale' => str_replace(array(' ', ".", '%%'), array('%', '%', '%'), $ospedale),
                'cliente' => $cliente->getId(),
            );

            $stmt = $connection->executeQuery($q, $params);

            $rows = $stmt->fetchAll();
        }
        if (count($rows) == 0) {
            $q = "   
    SELECT id, claimant FROM claims_h_pratiche p 
     WHERE p.claimant like :claimant2
       AND p.ospedale_id IN ( SELECT o.id
                                FROM claims_h_ospedali o
                               WHERE o.ospedale LIKE :ospedale ) 
       AND p.cliente_id = :cliente
    ";
            $params = array(
                'claimant2' => $this->percento($claimant),
                'ospedale' => str_replace(array(' ', ".", '%%'), array('%', '%', '%'), $ospedale),
                'cliente' => $cliente->getId(),
            );

            $stmt = $connection->executeQuery($q, $params);

            $rows = $stmt->fetchAll();
            if (count($rows) > 0) {
                $min = 99;
                $mini = 99;
                foreach ($rows as $i => $row) {
                    $l = levenshtein($claimant, $row['claimant']);
                    if ($l < $min) {
                        $min = $l;
                        $mini = $i;
                    }
                }
                if ($min > 3 && strpos(strtolower($rows[$mini]['claimant']), 'eredi')) {
                    $rows = array();
                } else {
                    $rows = array($rows[$mini]);
                }
            }
        }
        if (count($rows) == 0) {
            $claimants = explode(' ', $claimant);

            for ($i = 0; $i < count($claimants); $i++) {
                $c = '%';
                for ($j = 0; $j < count($claimants); $j++) {
                    if ($i != $j) {
                        $c .= $claimants[$j] . '%';
                    }
                }
                $q = "   
        SELECT id, claimant FROM claims_h_pratiche p 
         WHERE p.claimant like :claimant2
           AND p.ospedale_id IN ( SELECT o.id
                                    FROM claims_h_ospedali o
                                   WHERE o.ospedale LIKE :ospedale ) 
           AND p.cliente_id = :cliente
    ";
                $params = array(
                    'claimant2' => $c,
                    'ospedale' => str_replace(array(' ', ".", '%%'), array('%', '%', '%'), $ospedale),
                    'cliente' => $cliente->getId(),
                );

                $stmt = $connection->executeQuery($q, $params);

                $rows = $stmt->fetchAll();
                if (count($rows) > 0) {
                    $min = 99;
                    $mini = 99;
                    foreach ($rows as $i => $row) {
                        $l = levenshtein($claimant, $row['claimant']);
                        if ($l < $min) {
                            $min = $l;
                            $mini = $i;
                        }
                    }
                    if ($min > 3 && strpos(strtolower($rows[$mini]['claimant']), 'eredi') === false) {
                        $rows = array();
                    } else {
                        $rows = array($rows[$mini]);
                    }
                }
                if (count($rows) > 0) {
                    break;
                }
            }
        }
        $out = array();
        foreach ($rows as $i => $row) {
            $out[] = $this->find($row['id']);
        }
        return $out;
    }

    private function percento($stringa) {
        $out = '%';
        $stringa = strtolower($stringa);
        for ($i = 0; $i < strlen($stringa); $i++) {
            if (in_array($stringa{$i}, array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'))) {
                $out .= $stringa{$i} . '%';
            }
        }
        return $out;
    }

}

