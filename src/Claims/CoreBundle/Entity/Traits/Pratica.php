<?php

namespace Claims\CoreBundle\Entity\Traits;

trait Pratica
{
    /**
     * @var string
     *
     * @ORM\Column(name="codice", type="string", length=32)
     */
    private $codice;

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
     * @var \Claims\CoreBundle\Entity\StatoPratica
     * 
     * @ORM\ManyToOne(targetEntity="Claims\CoreBundle\Entity\StatoPratica")
     * @ORM\JoinColumn(name="stato_pratica_id", referencedColumnName="id", nullable=true)
     */
    private $statoPratica;

    /**
     * @var \Claims\CoreBundle\Entity\Priorita
     * 
     * @ORM\ManyToOne(targetEntity="Claims\CoreBundle\Entity\Priorita")
     * @ORM\JoinColumn(name="priorita_id", referencedColumnName="id", nullable=true)
     */
    private $priorita;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * Set codice
     *
     * @param string $codice
     * @return Pratica
     */
    public function setCodice($codice)
    {
        $this->codice = $codice;
    
        return $this;
    }

    /**
     * Get codice
     *
     * @return string 
     */
    public function getCodice()
    {
        return $this->codice;
    }

    /**
     * Set cliente
     *
     * @param integer $cliente
     * @return Pratica
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    
        return $this;
    }

    /**
     * Get cliente
     *
     * @return integer 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set gestore
     *
     * @param integer $gestore
     * @return Pratica
     */
    public function setGestore($gestore)
    {
        $this->gestore = $gestore;
    
        return $this;
    }

    /**
     * Get gestore
     *
     * @return integer 
     */
    public function getGestore()
    {
        return $this->gestore;
    }

    /**
     * Set statoPratica
     *
     * @param integer $statoPratica
     * @return Pratica
     */
    public function setStatoPratica($statoPratica)
    {
        $this->statoPratica = $statoPratica;
    
        return $this;
    }

    /**
     * Get statoPratica
     *
     * @return integer 
     */
    public function getStatoPratica()
    {
        return $this->statoPratica;
    }

    /**
     * Set priorita
     *
     * @param integer $priorita
     * @return Pratica
     */
    public function setPriorita($priorita)
    {
        $this->priorita = $priorita;
    
        return $this;
    }

    /**
     * Get priorita
     *
     * @return integer 
     */
    public function getPriorita()
    {
        return $this->priorita;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Pratica
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }
}
