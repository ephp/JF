<?php

namespace Claims\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatoPratica
 *
 * @ORM\Table(name="claims_stati_pratica")
 * @ORM\Entity(repositoryClass="Claims\CoreBundle\Entity\StatoPraticaRepository")
 */
class StatoPratica
{
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
     * @var string
     *
     * @ORM\Column(name="stato", type="string", length=32)
     */
    private $stato;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tab", type="boolean", nullable=true)
     */
    private $tab;

    /**
     * @var boolean
     *
     * @ORM\Column(name="stats", type="boolean", nullable=true)
     */
    private $stats;

    /**
     * @var boolean
     *
     * @ORM\Column(name="primo", type="boolean", nullable=true)
     */
    private $primo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="annota", type="boolean", nullable=true)
     */
    private $annota;

    /**
     * @var boolean
     *
     * @ORM\Column(name="chiudi", type="boolean", nullable=true)
     */
    private $chiudi;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cliente
     *
     * @param integer $cliente
     * @return StatoPratica
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
     * Set stato
     *
     * @param string $stato
     * @return StatoPratica
     */
    public function setStato($stato)
    {
        $this->stato = $stato;
    
        return $this;
    }

    /**
     * Get stato
     *
     * @return string 
     */
    public function getStato()
    {
        return $this->stato;
    }

    /**
     * Set tab
     *
     * @param boolean $tab
     * @return StatoPratica
     */
    public function setTab($tab)
    {
        $this->tab = $tab;
    
        return $this;
    }

    /**
     * Get tab
     *
     * @return boolean 
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * Set stats
     *
     * @param boolean $stats
     * @return StatoPratica
     */
    public function setStats($stats)
    {
        $this->stats = $stats;
    
        return $this;
    }

    /**
     * Get stats
     *
     * @return boolean 
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * Set primo
     *
     * @param boolean $primo
     * @return StatoPratica
     */
    public function setPrimo($primo)
    {
        $this->primo = $primo;
    
        return $this;
    }

    /**
     * Get primo
     *
     * @return boolean 
     */
    public function getPrimo()
    {
        return $this->primo;
    }

    /**
     * Set annota
     *
     * @param boolean $annota
     * @return StatoPratica
     */
    public function setAnnota($annota)
    {
        $this->annota = $annota;
    
        return $this;
    }

    /**
     * Get annota
     *
     * @return boolean 
     */
    public function getAnnota()
    {
        return $this->annota;
    }

    /**
     * Set chiudi
     *
     * @param boolean $chiudi
     * @return StatoPratica
     */
    public function setChiudi($chiudi)
    {
        $this->chiudi = $chiudi;
    
        return $this;
    }

    /**
     * Get chiudi
     *
     * @return boolean 
     */
    public function getChiudi()
    {
        return $this->chiudi;
    }
}
