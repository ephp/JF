<?php

namespace JF\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ordine
 *
 * @ORM\Table(name="jf_ordini")
 * @ORM\Entity(repositoryClass="JF\CoreBundle\Entity\OrdineRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Ordine {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \JF\ACLBundle\Entity\Cliente
     * 
     * @ORM\ManyToOne(targetEntity="JF\ACLBundle\Entity\Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", nullable=true)
     */
    private $cliente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creazione", type="datetime")
     */
    private $creazione;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cancellazione", type="datetime", nullable=true)
     */
    private $cancellazione;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ordinato", type="datetime", nullable=true)
     */
    private $ordinato;

    /**
     * @var boolean
     *
     * @ORM\Column(name="omaggio", type="boolean", nullable=true)
     */
    private $omaggio;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Prodotto", mappedBy="ordine", cascade="all")
     */
    private $prodotti;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prodotti = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set cliente
     *
     * @param integer $cliente
     * @return Ordine
     */
    public function setCliente($cliente) {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return integer 
     */
    public function getCliente() {
        return $this->cliente;
    }

    /**
     * Set creazione
     *
     * @param \DateTime $creazione
     * @return Ordine
     */
    public function setCreazione($creazione) {
        $this->creazione = $creazione;

        return $this;
    }

    /**
     * Get creazione
     *
     * @return \DateTime 
     */
    public function getCreazione() {
        return $this->creazione;
    }

    /**
     * Set cancellazione
     *
     * @param \DateTime $cancellazione
     * @return Ordine
     */
    public function setCancellazione($cancellazione) {
        $this->cancellazione = $cancellazione;

        return $this;
    }

    /**
     * Get cancellazione
     *
     * @return \DateTime 
     */
    public function getCancellazione() {
        return $this->cancellazione;
    }

    /**
     * Set ordinato
     *
     * @param \DateTime $ordinato
     * @return Ordine
     */
    public function setOrdinato($ordinato) {
        $this->ordinato = $ordinato;

        return $this;
    }

    /**
     * Get ordinato
     *
     * @return \DateTime 
     */
    public function getOrdinato() {
        return $this->ordinato;
    }

    /**
     * Add prodotti
     *
     * @param \JF\CoreBundle\Entity\Prodotto $prodotti
     * @return Ordine
     */
    public function addProdotti(\JF\CoreBundle\Entity\Prodotto $prodotti)
    {
        $this->prodotti[] = $prodotti;
    
        return $this;
    }

    /**
     * Remove prodotti
     *
     * @param \JF\CoreBundle\Entity\Prodotto $prodotti
     */
    public function removeProdotti(\JF\CoreBundle\Entity\Prodotto $prodotti)
    {
        $this->prodotti->removeElement($prodotti);
    }

    /**
     * Get prodotti
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProdotti()
    {
        return $this->prodotti;
    }

    public function getOmaggio() {
        return $this->omaggio;
    }

    public function setOmaggio($omaggio) {
        $this->omaggio = $omaggio;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->creazione = new \DateTime();
    }
}