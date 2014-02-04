<?php

namespace Claims\HAuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gruppo
 *
 * @ORM\Table(name="claims_h_audit_gruppi_question")
 * @ORM\Entity(repositoryClass="Claims\HAuditBundle\Entity\GruppoRepository")
 */
class Gruppo
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="titolo", type="string", length=255)
     */
    private $titolo;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordine", type="integer")
     */
    private $ordine;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $show;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Question", mappedBy="gruppo", cascade="all")
     */
    private $question;


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
     * Set title
     *
     * @param string $title
     * @return Gruppo
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set titolo
     *
     * @param string $titolo
     * @return Gruppo
     */
    public function setTitolo($titolo)
    {
        $this->titolo = $titolo;
    
        return $this;
    }

    /**
     * Get titolo
     *
     * @return string 
     */
    public function getTitolo()
    {
        return $this->titolo;
    }

    /**
     * Set ordine
     *
     * @param integer $ordine
     * @return Gruppo
     */
    public function setOrdine($ordine)
    {
        $this->ordine = $ordine;
    
        return $this;
    }

    /**
     * Get ordine
     *
     * @return integer 
     */
    public function getOrdine()
    {
        return $this->ordine;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->question = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add question
     *
     * @param \Claims\HAuditBundle\Entity\Question $question
     * @return Gruppo
     */
    public function addQuestion(\Claims\HAuditBundle\Entity\Question $question)
    {
        $this->question[] = $question;
    
        return $this;
    }

    /**
     * Remove question
     *
     * @param \Claims\HAuditBundle\Entity\Question $question
     */
    public function removeQuestion(\Claims\HAuditBundle\Entity\Question $question)
    {
        $this->question->removeElement($question);
    }

    /**
     * Get question
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestion()
    {
        return $this->question;
    }
    
    public function getShow() {
        return $this->show;
    }

    public function setShow($show) {
        $this->show = $show;
        return $this;
    }

    public function __toString() {
        return $this->titolo;
    }

    public function getTitoloLocale($locale) {
        switch(strtolower($locale)){
            case 'it':
            case 'it_it':
            case 'ita':
                return $this->titolo ?: $this->title;
            default:
                return $this->title;
        }
    }
}