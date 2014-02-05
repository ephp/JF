<?php

namespace Claims\HAuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuditQuestion
 *
 * @ORM\Table(name="claims_h_audit_audit_question")
 * @ORM\Entity(repositoryClass="Claims\HAuditBundle\Entity\AuditQuestionRepository")
 */
class AuditQuestion
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
     * @var Audit
     * 
     * @ORM\ManyToOne(targetEntity="Audit")
     * @ORM\JoinColumn(name="audit_id", referencedColumnName="id")
     */
    private $audit;

    /**
     * @var Question
     * 
     * @ORM\ManyToOne(targetEntity="Question")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

    /**
     * @var Gruppo
     * 
     * @ORM\ManyToOne(targetEntity="Gruppo")
     * @ORM\JoinColumn(name="gruppo_id", referencedColumnName="id", nullable=true)
     */
    private $gruppo;

    /**
     * @var Sottogruppo
     * 
     * @ORM\ManyToOne(targetEntity="Sottogruppo")
     * @ORM\JoinColumn(name="sottogruppo_id", referencedColumnName="id", nullable=true)
     */
    private $sottogruppo;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordine", type="integer")
     */
    private $ordine;


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
     * Set audit
     *
     * @param integer $audit
     * @return AuditQuestion
     */
    public function setAudit($audit)
    {
        $this->audit = $audit;
    
        return $this;
    }

    /**
     * Get audit
     *
     * @return integer 
     */
    public function getAudit()
    {
        return $this->audit;
    }

    /**
     * Set question
     *
     * @param integer $question
     * @return AuditQuestion
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    
        return $this;
    }

    /**
     * Get question
     *
     * @return integer 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set ordine
     *
     * @param integer $ordine
     * @return AuditQuestion
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
     * 
     * @param \Claims\HAuditBundle\Entity\Gruppo $gruppo
     * @return \Claims\HAuditBundle\Entity\AuditQuestion
     */
    public function setGruppo(Gruppo $gruppo) {
        $this->gruppo = $gruppo;
        return $this;
    }

    /**
     * 
     * @return Gruppo
     */
    public function getGruppo() {
        return $this->gruppo;
    }

    /**
     * 
     * @param \Claims\HAuditBundle\Entity\Sottogruppo $gruppo
     * @return \Claims\HAuditBundle\Entity\AuditQuestion
     */
    public function setSottogruppo(Sottogruppo $gruppo) {
        $this->sottogruppo = $gruppo;
        return $this;
    }

    /**
     * 
     * @return Sottogruppo
     */
    public function getSottogruppo() {
        return $this->sottogruppo;
    }

}