<?php

namespace Claims\HBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EventoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventoRepository extends EntityRepository {

    public function cancellaTipoDaPratica(Pratica $pratica, \Ephp\CalendarBundle\Entity\Tipo $tipo, $titolo = null, $note = null) {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $params = array('pratica' => $pratica->getId(), 'tipo' => $tipo->getId());
        if ($titolo) {
            $params['titolo'] = $titolo;
        }
        if ($note) {
            $params['note'] = $note;
        }
        $conn->executeUpdate("
DELETE 
  FROM claims_h_eventi
 WHERE pratica_id = :pratica
   AND tipo_id = :tipo " .
                ($titolo ? " AND titolo LIKE :titolo " : "") .
                ($note ? " AND note LIKE :note " : "")
                , $params);
    }

    public function calendarioMese(\JF\ACLBundle\Entity\Gestore $gestore, $mese, $anno, $giorno = null) {
        $mese = intval($mese);
        if ($mese < 10) {
            $mese = "0{$mese}";
        }
        if ($giorno) {
            $giorno = intval($giorno);
            if ($giorno < 10) {
                $giorno = "0{$giorno}";
            }
        }
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $stmt = $conn->executeQuery($giorno ? "
SELECT e.id 
  FROM claims_h_eventi e
 WHERE e.gestore_id = :gestore
   AND e.data_ora LIKE '{$anno}-{$mese}-{$giorno}%'
            " : "
SELECT e.id 
  FROM claims_h_eventi e
 WHERE e.gestore_id = :gestore
   AND e.data_ora LIKE '{$anno}-{$mese}%'
            ", array('gestore' => $gestore->getId()));
        $out = $stmt->fetchAll();
        $ids = array(0);
        foreach ($out as $id) {
            $ids[] = $id['id'];
        }
        return $this->findBy(array('id' => $ids), array('data_ora' => 'ASC'));
    }

}
