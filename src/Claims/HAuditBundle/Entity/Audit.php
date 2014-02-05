<?php

namespace Claims\HAuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Audit
 *
 * @ORM\Table(name="claims_h_audit")
 * @ORM\Entity(repositoryClass="Claims\HAuditBundle\Entity\AuditRepository")
 */
class Audit {

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
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     */
    private $cliente;
    
    /**
     * @var string
     *
     * @ORM\Column(name="luogo", type="string", length=255)
     */
    private $luogo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="giorno", type="date")
     */
    private $giorno;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Pratica", mappedBy="audit", cascade="all")
     */
    private $pratiche;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AuditQuestion", mappedBy="audit", cascade="all")
     * @ORM\OrderBy({"ordine" = "ASC"})
     */
    private $question;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set luogo
     *
     * @param string $luogo
     * @return Audit
     */
    public function setLuogo($luogo) {
        $this->luogo = $luogo;

        return $this;
    }

    /**
     * Get luogo
     *
     * @return string 
     */
    public function getLuogo() {
        return $this->luogo;
    }

    /**
     * Set giorno
     *
     * @param \DateTime $giorno
     * @return Audit
     */
    public function setGiorno($giorno) {
        $this->giorno = $giorno;

        return $this;
    }

    /**
     * Get giorno
     *
     * @return \DateTime 
     */
    public function getGiorno() {
        return $this->giorno;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Audit
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
     * Constructor
     */
    public function __construct()
    {
        $this->pratiche = new \Doctrine\Common\Collections\ArrayCollection();
        $this->question = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set cliente
     *
     * @param \JF\ACLBundle\Entity\Cliente $cliente
     * @return Audit
     */
    public function setCliente(\JF\ACLBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;
    
        return $this;
    }

    /**
     * Get cliente
     *
     * @return \JF\ACLBundle\Entity\Cliente 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Add pratiche
     *
     * @param \Claims\HAuditBundle\Entity\Pratica $pratiche
     * @return Audit
     */
    public function addPratiche(\Claims\HAuditBundle\Entity\Pratica $pratiche)
    {
        $this->pratiche[] = $pratiche;
    
        return $this;
    }

    /**
     * Remove pratiche
     *
     * @param \Claims\HAuditBundle\Entity\Pratica $pratiche
     */
    public function removePratiche(\Claims\HAuditBundle\Entity\Pratica $pratiche)
    {
        $this->pratiche->removeElement($pratiche);
    }

    /**
     * Get pratiche
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPratiche()
    {
        return $this->pratiche;
    }

    /**
     * Add question
     *
     * @param \Claims\HAuditBundle\Entity\AuditQuestion $question
     * @return Audit
     */
    public function addQuestion(\Claims\HAuditBundle\Entity\AuditQuestion $question)
    {
        $this->question[] = $question;
    
        return $this;
    }

    /**
     * Remove question
     *
     * @param \Claims\HAuditBundle\Entity\AuditQuestion $question
     */
    public function removeQuestion(\Claims\HAuditBundle\Entity\AuditQuestion $question)
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
    
    public function __toString() {
        return $this->luogo;
    }
    
    public function getResponses() {
        $n = 0;
        foreach($this->pratiche as $pratica) {
            /* @var $pratica Pratica */
            $n += count($pratica->getQuestion());
        }
        return $n;
    }
    
    public function getGroup($ordine) {
        $out = array(
            'obj' => null,
            'questions' => array(),
        );
        foreach($this->question as $question) {
            /* @var $question AuditQuestion */
            if($question->getGruppo()->getOrdine() == $ordine) {
                $out['obj'] = $question->getGruppo();
                if($question->getSottogruppo()) {
                    $out['questions']['sg'.$question->getSottogruppo()->getId()]['obj'] = $question->getSottogruppo();
                    $out['questions']['sg'.$question->getSottogruppo()->getId()]['questions']['d'.$question->getId()] = $question;
                } else {
                    $out['questions']['q'.$question->getId()] = $question;
                }
            }
        }
        return $out;
    }
}