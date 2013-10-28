<?php

namespace Claims\HBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evento
 *
 * @ORM\Table(name="claims_h_eventi")
 * @ORM\Entity(repositoryClass="Claims\HBundle\Entity\EventoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Evento implements \JF\CalendarBundle\Interfaces\IEvento {

    use \JF\CalendarBundle\Entity\Traits\Evento;

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
     * @var integer
     *
     * @ORM\Column(name="delta_g", type="integer", nullable=true)
     */
    private $deltaG;
    
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
     * @return Evento 
     */
    public function setPratica(Pratica $pratica) {
        $this->pratica = $pratica;
        
        return $this;
    }

    /**
     * Get deltaG
     *
     * @return integer 
     */
    public function getDeltaG() {
        return $this->deltaG;
    }

    
    /**
     * Set deltaG
     *
     * @var $deltaG integer
     * @return Evento 
     */
    public function setDeltaG($deltaG) {
        $this->deltaG = $deltaG;
        
        return $this;
    }


    
}