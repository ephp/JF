<?php

namespace Claims\HBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ospedale
 *
 * @ORM\Table(name="claims_h_ospedali")
 * @ORM\Entity(repositoryClass="Claims\HBundle\Entity\OspedaleRepository")
 */
class Ospedale
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
     * @var string
     *
     * @ORM\Column(name="ospedale", type="string", length=32)
     */
    private $ospedale;

    /**
     * @var string
     *
     * @ORM\Column(name="sigla", type="string", length=16)
     */
    private $sigla;

    /**
     * @var string
     *
     * @ORM\Column(name="gruppo", type="string", length=32)
     */
    private $gruppo;

    /**
     * @var string
     *
     * @ORM\Column(name="sigla_gruppo", type="string", length=16)
     */
    private $siglaGruppo;

    /**
     * @var Sistema
     * 
     * @ORM\ManyToOne(targetEntity="Sistema")
     * @ORM\JoinColumn(name="sistema_id", referencedColumnName="id", nullable=true)
     */
    private $sistema;
    
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
     * Set ospedale
     *
     * @param string $ospedale
     * @return Ospedale
     */
    public function setOspedale($ospedale)
    {
        $this->ospedale = $ospedale;
    
        return $this;
    }

    /**
     * Get ospedale
     *
     * @return string 
     */
    public function getOspedale()
    {
        return $this->ospedale;
    }

    /**
     * Set sigla
     *
     * @param string $sigla
     * @return Ospedale
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;
    
        return $this;
    }

    /**
     * Get sigla
     *
     * @return string 
     */
    public function getSigla()
    {
        return $this->sigla;
    }

    /**
     * Set gruppo
     *
     * @param string $gruppo
     * @return Ospedale
     */
    public function setGruppo($gruppo)
    {
        $this->gruppo = $gruppo;
    
        return $this;
    }

    /**
     * Get gruppo
     *
     * @return string 
     */
    public function getGruppo()
    {
        return $this->gruppo;
    }

    /**
     * Set siglaGruppo
     *
     * @param string $siglaGruppo
     * @return Ospedale
     */
    public function setSiglaGruppo($siglaGruppo)
    {
        $this->siglaGruppo = $siglaGruppo;
    
        return $this;
    }

    /**
     * Get siglaGruppo
     *
     * @return string 
     */
    public function getSiglaGruppo()
    {
        return $this->siglaGruppo;
    }

    /**
     * Set sistema
     *
     * @param \Claims\HBundle\Entity\Sistema $sistema
     * @return Ospedale
     */
    public function setSistema(\Claims\HBundle\Entity\Sistema $sistema = null)
    {
        $this->sistema = $sistema;
    
        return $this;
    }

    /**
     * Get sistema
     *
     * @return \Claims\HBundle\Entity\Sistema 
     */
    public function getSistema()
    {
        return $this->sistema;
    }
    
    public function __toString() {
        return "{$this->sigla} - {$this->ospedale}";
    }
}