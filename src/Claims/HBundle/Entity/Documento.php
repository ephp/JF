<?php

namespace Claims\HBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evento
 *
 * @ORM\Table(name="claims_h_documenti")
 * @ORM\Entity(repositoryClass="Claims\HBundle\Entity\DocumentoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Documento {

    use \Claims\CoreBundle\Entity\Traits\Documento;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Pratica
     * 
     * @ORM\ManyToOne(targetEntity="Pratica")
     * @ORM\JoinColumn(name="pratica_id", referencedColumnName="id")
     */
    private $pratica;

    /**
     * @var Evento
     * 
     * @ORM\ManyToOne(targetEntity="Evento")
     * @ORM\JoinColumn(name="evento_id", referencedColumnName="id", nullable=true)
     */
    private $evento;

    /**
     * @var integer
     *
     * @ORM\Column(name="allegato", type="integer", nullable=true)
     */
    private $allegato;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get pratica
     *
     * @return Pratica 
     */
    public function getPratica() {
        return $this->pratica;
    }

    /**
     * Set pratica
     *
     * @var $pratica Pratica
     * @return Documento 
     */
    public function setPratica(Pratica $pratica) {
        $this->pratica = $pratica;

        return $this;
    }

    /**
     * Get evento
     *
     * @return Evento 
     */
    public function getEvento() {
        return $this->evento;
    }

    /**
     * Set evento
     *
     * @var $pratica Evento
     * @return Documento 
     */
    public function setEvento(Evento $evento) {
        $this->evento = $evento;
        $this->setPratica($evento->getPratica());
        $this->setGestore($evento->getPratica()->getGestore());
        $this->setCliente($evento->getPratica()->getCliente());
        return $this;
    }

    public function getAllegato() {
        return $this->allegato;
    }

    public function setAllegato($allegato) {
        $this->allegato = $allegato;
    }

}
