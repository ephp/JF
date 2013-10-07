<?php

namespace JF\ACLBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Licenza
 *
 * @ORM\Table(name="acl_licenze")
 * @ORM\Entity(repositoryClass="JF\ACLBundle\Entity\LicenzaRepository")
 */
class Licenza {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Cliente
     * 
     * @ORM\ManyToOne(targetEntity="Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", nullable=true)
     */
    private $cliente;

    /**
     * @var \JF\CoreBundle\Entity\Licenza
     * 
     * @ORM\ManyToOne(targetEntity="JF\CoreBundle\Entity\Licenza")
     * @ORM\JoinColumn(name="licenza_id", referencedColumnName="id", nullable=true)
     */
    private $licenza;

    /**
     * @var \JF\CoreBundle\Entity\Licenza
     * 
     * @ORM\ManyToOne(targetEntity="JF\CoreBundle\Entity\Licenza")
     * @ORM\JoinColumn(name="licenza_precedente_id", referencedColumnName="id", nullable=true)
     */
    private $licenzaPrecedente;

    /**
     * @var \JF\CoreBundle\Entity\Gruppo
     *
     * @ORM\ManyToOne(targetEntity="JF\CoreBundle\Entity\Gruppo")
     * @ORM\JoinColumn(name="gruppo_id", referencedColumnName="id", nullable=true)
     */
    private $gruppo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="acquisto", type="datetime")
     */
    private $acquisto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pagamento", type="datetime", nullable=true)
     */
    private $pagamento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="scadenza", type="datetime", nullable=true)
     */
    private $scadenza;

    /**
     * @var integer
     *
     * @ORM\Column(name="fattura", type="integer", nullable=true)
     */
    private $fattura;

    function __construct() {
        $this->acquisto = new \DateTime();
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
     * Set gruppo
     *
     * @param \JF\CoreBundle\Entity\Gruppo $gruppo
     * @return Licenza
     */
    public function setGruppo(\JF\CoreBundle\Entity\Gruppo $gruppo) {
        $this->gruppo = $gruppo;

        return $this;
    }

    /**
     * Get gruppo
     *
     * @return \JF\CoreBundle\Entity\Gruppo 
     */
    public function getGruppo() {
        return $this->gruppo;
    }

    /**
     * Set acquisto
     *
     * @param \DateTime $acquisto
     * @return Licenza
     */
    public function setAcquisto($acquisto) {
        $this->acquisto = $acquisto;

        return $this;
    }

    /**
     * Get acquisto
     *
     * @return \DateTime 
     */
    public function getAcquisto() {
        return $this->acquisto;
    }

    /**
     * Set pagamento
     *
     * @param \DateTime $pagamento
     * @return Licenza
     */
    public function setPagamento($pagamento) {
        $this->pagamento = $pagamento;
        if ($this->getLicenza()->getDurata()) {
            $scadenza = $this->getLicenza()->getDurata() + intval($this->acquisto->diff($this->pagamento, true)->format('a'));
            $this->scadenza = $this->pagamento->add(new \DateInterval("P{$scadenza}D"));
        }

        return $this;
    }

    /**
     * Get pagamento
     *
     * @return \DateTime 
     */
    public function getPagamento() {
        return $this->pagamento;
    }

    /**
     * Set scadenza
     *
     * @param \DateTime $scadenza
     * @return Licenza
     */
    public function setScadenza($scadenza) {
        $this->scadenza = $scadenza;

        return $this;
    }

    /**
     * Get scadenza
     *
     * @return \DateTime 
     */
    public function getScadenza() {
        return $this->scadenza;
    }

    /**
     * Set fattura
     *
     * @param integer $fattura
     * @return Licenza
     */
    public function setFattura($fattura) {
        $this->fattura = $fattura;

        return $this;
    }

    /**
     * Get fattura
     *
     * @return integer 
     */
    public function getFattura() {
        return $this->fattura;
    }

    /**
     * Set cliente
     *
     * @param \JF\ACLBundle\Entity\Cliente $cliente
     * @return Licenza
     */
    public function setCliente(\JF\ACLBundle\Entity\Cliente $cliente = null) {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \JF\ACLBundle\Entity\Cliente 
     */
    public function getCliente() {
        return $this->cliente;
    }

    /**
     * Set licenza
     *
     * @param \JF\CoreBundle\Entity\Licenza $licenza
     * @return Licenza
     */
    public function setLicenza(\JF\CoreBundle\Entity\Licenza $licenza = null) {
        $this->licenza = $licenza;
        $this->gruppo = $licenza->getGruppo();

        return $this;
    }

    /**
     * Get licenza
     *
     * @return \JF\CoreBundle\Entity\Licenza 
     */
    public function getLicenza() {
        return $this->licenza;
    }
    
    /**
     * Set licenzaPrecedente
     *
     * @param \JF\CoreBundle\Entity\Licenza $licenzaPrecedente
     * @return LicenzaPrecedente
     */
    public function setLicenzaPrecedente(\JF\CoreBundle\Entity\Licenza $licenzaPrecedente = null) {
        $this->licenzaPrecedente = $licenzaPrecedente;

        return $this;
    }

    /**
     * Get licenzaPrecedente
     *
     * @return \JF\CoreBundle\Entity\Licenza 
     */
    public function getLicenzaPrecedente() {
        return $this->licenzaPrecedente;
    }

    public function isExpired() {
        if (!$this->scadenza) {
            return false;
        }
        return $this->scadenza->diff(new \DateTime())->format('R') == '-';
    }

    public function isActive() {
        if (!$this->scadenza) {
            return true;
        }
        return $this->scadenza->diff(new \DateTime())->format('R') != '-';
    }

    public function info() {
            return array(
                'gruppo' => $this->gruppo,
                'codice' => $this->licenza->getSigla(),
                'attiva' => $this->isActive(),
            );
    }

}