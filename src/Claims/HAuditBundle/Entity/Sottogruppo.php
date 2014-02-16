<?php

namespace Claims\HAuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gruppo
 *
 * @ORM\Table(name="claims_h_audit_sottogruppi_question")
 * @ORM\Entity(repositoryClass="Claims\HAuditBundle\Entity\SottogruppoRepository")
 */
class Sottogruppo
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
     * @var integer
     *
     * @ORM\Column(name="remote_id", type="integer")
     */
    private $remoteId;
    
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
     * @var boolean
     *
     * @ORM\Column(name="multiplo", type="boolean", nullable=true)
     */
    private $multiplo;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Question", mappedBy="sottogruppo", cascade="all")
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
    
    public function getRemoteId() {
        return $this->remoteId;
    }

    public function setRemoteId($remoteId) {
        $this->remoteId = $remoteId;
        return $this;
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
    
    public function getMultiplo() {
        return $this->multiplo;
    }

    public function setMultiplo($multiplo) {
        $this->multiplo = $multiplo;
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