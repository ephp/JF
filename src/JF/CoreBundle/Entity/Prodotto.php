<?php

namespace JF\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prodotto
 *
 * @ORM\Table(name="jf_ordini_prodotti")
 * @ORM\Entity(repositoryClass="JF\CoreBundle\Entity\ProdottoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Prodotto {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Ordine
     * 
     * @ORM\ManyToOne(targetEntity="JF\CoreBundle\Entity\Ordine")
     * @ORM\JoinColumn(name="ordine_id", referencedColumnName="id")
     */
    private $ordine;

    /**
     * @var Licenza
     * 
     * @ORM\ManyToOne(targetEntity="JF\CoreBundle\Entity\Licenza")
     * @ORM\JoinColumn(name="licenza_id", referencedColumnName="id")
     */
    private $licenza;

    /**
     * @var float
     *
     * @ORM\Column(name="prezzo", type="decimal")
     */
    private $prezzo;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantita", type="integer")
     */
    private $quantita;

    /**
     * @var float
     *
     * @ORM\Column(name="totale", type="decimal")
     */
    private $totale;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set licenza
     *
     * @param integer $licenza
     * @return Prodotto
     */
    public function setLicenza($licenza) {
        $this->licenza = $licenza;

        return $this;
    }

    /**
     * Get licenza
     *
     * @return integer 
     */
    public function getLicenza() {
        return $this->licenza;
    }

    /**
     * Set prezzo
     *
     * @param float $prezzo
     * @return Prodotto
     */
    public function setPrezzo($prezzo) {
        $this->prezzo = $prezzo;

        return $this;
    }

    /**
     * Get prezzo
     *
     * @return float 
     */
    public function getPrezzo() {
        return $this->prezzo;
    }

    /**
     * Set quantita
     *
     * @param integer $quantita
     * @return Prodotto
     */
    public function setQuantita($quantita) {
        $this->quantita = $quantita;

        return $this;
    }

    /**
     * Get quantita
     *
     * @return integer 
     */
    public function getQuantita() {
        return $this->quantita;
    }

    /**
     * Set totale
     *
     * @param float $totale
     * @return Prodotto
     */
    public function setTotale($totale) {
        $this->totale = $totale;

        return $this;
    }

    /**
     * Get totale
     *
     * @return float 
     */
    public function getTotale() {
        return $this->totale;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Prodotto
     */
    public function setNote($note) {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * Set ordine
     *
     * @param \JF\CoreBundle\Entity\Ordine $ordine
     * @return Prodotto
     */
    public function setOrdine(\JF\CoreBundle\Entity\Ordine $ordine = null) {
        $this->ordine = $ordine;

        return $this;
    }

    /**
     * Get ordine
     *
     * @return \JF\CoreBundle\Entity\Ordine 
     */
    public function getOrdine() {
        return $this->ordine;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->totale = $this->getPrezzo() * $this->getQuantita();
    }

}