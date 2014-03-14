<?php

namespace Claims\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Priorita
 *
 * @ORM\Table(name="claims_priorita")
 * @ORM\Entity(repositoryClass="Claims\CoreBundle\Entity\PrioritaRepository")
 */
class Priorita {

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
     * @ORM\Column(name="priorita", type="string", length=32)
     */
    private $priorita;

    /**
     * @var string
     *
     * @ORM\Column(name="css", type="string", length=16, nullable=true)
     */
    private $css;

    /**
     * @var string
     *
     * @ORM\Column(name="on_assign", type="string", length=16, nullable=true)
     */
    private $onAssign;

    /**
     * @var string
     *
     * @ORM\Column(name="area", type="string", length=16, nullable=true)
     */
    private $area;

    /**
     * @var boolean
     *
     * @ORM\Column(name="menu_tendina", type="boolean", nullable=true)
     */
    private $show;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set priorita
     *
     * @param string $priorita
     * @return Priorita
     */
    public function setPriorita($priorita) {
        $this->priorita = $priorita;

        return $this;
    }

    /**
     * Get priorita
     *
     * @return string 
     */
    public function getPriorita() {
        return $this->priorita;
    }

    /**
     * Set css
     *
     * @param string $css
     * @return Priorita
     */
    public function setCss($css) {
        $this->css = $css;

        return $this;
    }

    /**
     * Get css
     *
     * @return string 
     */
    public function getCss() {
        return $this->css;
    }

    /**
     * Set onAssign
     *
     * @param string $onAssign
     * @return Priorita
     */
    public function setOnAssign($onAssign) {
        $this->onAssign = $onAssign;

        return $this;
    }

    /**
     * Get onAssign
     *
     * @return string 
     */
    public function getOnAssign() {
        return $this->onAssign;
    }

    /**
     * Set show
     *
     * @param boolean $show
     * @return Priorita
     */
    public function setShow($show) {
        $this->show = $show;

        return $this;
    }

    /**
     * Get show
     *
     * @return string 
     */
    public function getShow() {
        return $this->show;
    }

    public function getArea() {
        return $this->area;
    }

    public function setArea($area) {
        $this->area = $area;
        return $this;
    }

    public function __toString() {
        return $this->priorita;
    }

}
