<?php

namespace Claims\HBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EventoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentoRepository extends EntityRepository {

    public function cancellaTipoDaPratica(Pratica $pratica, \Ephp\CalendarBundle\Entity\Tipo $tipo) {
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
  FROM claims_h_documenti
 WHERE pratica_id = :pratica
   AND tipo_id = :tipo ", $params);
    }

}