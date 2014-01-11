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
     * @ORM\Column(name="note", type="text")
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

}
