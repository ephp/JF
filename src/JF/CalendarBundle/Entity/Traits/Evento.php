<?php

namespace JF\CalendarBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Evento {

    use \Ephp\CalendarBundle\Entity\Traits\Evento;

    /**
     * @var \JF\ACLBundle\Entity\Cliente
     * 
     * @ORM\ManyToOne(targetEntity="JF\ACLBundle\Entity\Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     */
    private $cliente;

    /**
     * @var \JF\ACLBundle\Entity\Gestore
     * 
     * @ORM\ManyToOne(targetEntity="JF\ACLBundle\Entity\Gestore")
     * @ORM\JoinColumn(name="gestore_id", referencedColumnName="id", nullable=true)
     */
    private $gestore;


    /**
     * Set cliente
     *
     * @param \JF\ACLBundle\Entity\Cliente $cliente
     * @return Evento
     */
    public function setCliente($cliente) {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \Ephp\CalendarBundle\Entity\Cliente 
     */
    public function getCliente() {
        return $this->cliente;
    }

    /**
     * Set gestore
     *
     * @param \JF\ACLBundle\Entity\Gestore $gestore
     * @return Evento
     */
    public function setGestore($gestore) {
        $this->gestore = $gestore;

        return $this;
    }

    /**
     * Get gestore
     *
     * @return \JF\ACLBundle\Entity\Gestore 
     */
    public function getGestore() {
        return $this->gestore;
    }

}