<?php

namespace Claims\HAuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Criteria;

/**
 * Pratica
 *
 * @ORM\Table(name="claims_h_audit_pratiche")
 * @ORM\Entity(repositoryClass="Claims\HAuditBundle\Entity\PraticaRepository")
 */
class Pratica {

    use \Claims\CoreBundle\Entity\Traits\Pratica;

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
     * @var integer
     *
     * @ORM\Column(name="gruppo", type="string", length=64)
     */
    private $gruppo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mese", type="string", length=16, nullable=true)
     */
    private $mese;

    /**
     * @var integer
     *
     * @ORM\Column(name="ospedale", type="string", length=64)
     */
    private $ospedale;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mf_ref", type="string", length=32, nullable=true)
     */
    private $mfRef;

    /**
     * @var integer
     *
     * @ORM\Column(name="anno", type="integer", nullable=true)
     */
    private $anno;

    /**
     * @var integer
     *
     * @ORM\Column(name="tpa", type="string", length=32)
     */
    private $tpa;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ds_code", type="string", length=64, nullable=true)
     */
    private $dsCode;

    /**
     * @var string
     *
     * @ORM\Column(name="claimant", type="string", length=255)
     */
    private $claimant;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var float
     *
     * @ORM\Column(name="payment", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $payment;

    /**
     * @var float
     *
     * @ORM\Column(name="reserve", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $reserve;

    /**
     * @var float
     *
     * @ORM\Column(name="pro_reserve", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $proReserve;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dol", type="date", nullable=true)
     */
    private $dol;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="don", type="date", nullable=true)
     */
    private $don;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_import", type="date")
     */
    private $dataImport;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="PraticaQuestion", mappedBy="pratica", cascade="all")
     */
    private $question;

    /*
     * AUDIT
     */

    /**
     * @var string
     *
     * @ORM\Column(name="audit", type="text", nullable=true)
     */
    private $auditText;

    /**
     * @var string
     *
     * @ORM\Column(name="azioni", type="text", nullable=true)
     */
    private $azioni;

    /**
     * @var integer
     *
     * @ORM\Column(name="percentuale", type="integer", nullable=true)
     */
    private $percentuale;

    /**
     * @var decimal
     *
     * @ORM\Column(name="worstcase_scenario", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $worstcaseScenario;

    /**
     * @var decimal
     *
     * @ORM\Column(name="proposed_reserve", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $proposedReserve;

    /**
     * @var string
     *
     * @ORM\Column(name="fact", type="text", nullable=true)
     */
    private $fact;

    /**
     * @var string
     *
     * @ORM\Column(name="investigation_handling", type="text", nullable=true)
     */
    private $investigationHandling;

    /**
     * @var string
     *
     * @ORM\Column(name="liability", type="text", nullable=true)
     */
    private $liability;

    /**
     * @var string
     *
     * @ORM\Column(name="quantum", type="text", nullable=true)
     */
    private $quantum;

    /**
     * @var string
     *
     * @ORM\Column(name="negotiation_handling", type="text", nullable=true)
     */
    private $negotiationHandling;

    /**
     * @var string
     *
     * @ORM\Column(name="proceeding_handling", type="text", nullable=true)
     */
    private $proceedingHandling;

    /**
     * @var string
     *
     * @ORM\Column(name="our_comments", type="text", nullable=true)
     */
    private $ourComments;

    /**
     * @var string
     *
     * @ORM\Column(name="nl_damage", type="text", nullable=true)
     */
    private $nlDamage;

    /**
     * Constructor
     */
    public function __construct() {
        $this->question = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param string $gruppo
     * @return Pratica
     */
    public function setGruppo($gruppo) {
        $this->gruppo = $gruppo;

        return $this;
    }

    /**
     * Get gruppo
     *
     * @return string
     */
    public function getGruppo() {
        return $this->gruppo;
    }

    /**
     * Set mese
     *
     * @param string $mese
     * @return Pratica
     */
    public function setMese($mese) {
        $this->mese = $mese;

        return $this;
    }

    /**
     * Get mese
     *
     * @return string 
     */
    public function getMese() {
        return $this->mese;
    }

    /**
     * Set ospedale
     *
     * @param string $ospedale
     * @return Pratica
     */
    public function setOspedale($ospedale) {
        $this->ospedale = $ospedale;

        return $this;
    }

    /**
     * Get ospedale
     *
     * @return string 
     */
    public function getOspedale() {
        return $this->ospedale;
    }

    /**
     * Set mfRef
     *
     * @param string $mfRef
     * @return Pratica
     */
    public function setMfRef($mfRef) {
        $this->mfRef = $mfRef;

        return $this;
    }

    /**
     * Get mfRef
     *
     * @return string 
     */
    public function getMfRef() {
        return $this->mfRef;
    }

    /**
     * Set anno
     *
     * @param integer $anno
     * @return Pratica
     */
    public function setAnno($anno) {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return integer 
     */
    public function getAnno() {
        return $this->anno;
    }

    /**
     * Set tpa
     *
     * @param string $tpa
     * @return Pratica
     */
    public function setTpa($tpa) {
        $this->tpa = $tpa;

        return $this;
    }

    /**
     * Get tpa
     *
     * @return string 
     */
    public function getTpa() {
        return $this->tpa;
    }

    /**
     * Set dsCode
     *
     * @param string $dsCode
     * @return Pratica
     */
    public function setDsCode($dsCode) {
        $this->dsCode = $dsCode;

        return $this;
    }

    /**
     * Get dsCode
     *
     * @return string 
     */
    public function getDsCode() {
        return $this->dsCode;
    }

    /**
     * Set claimant
     *
     * @param string $claimant
     * @return Pratica
     */
    public function setClaimant($claimant) {
        $this->claimant = $claimant;

        return $this;
    }

    /**
     * Get claimant
     *
     * @return string 
     */
    public function getClaimant() {
        return $this->claimant;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Pratica
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set payment
     *
     * @param float $payment
     * @return Pratica
     */
    public function setPayment($payment) {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return float 
     */
    public function getPayment() {
        return $this->payment;
    }

    /**
     * Set reserve
     *
     * @param float $reserve
     * @return Pratica
     */
    public function setReserve($reserve) {
        $this->reserve = $reserve;

        return $this;
    }

    /**
     * Get reserve
     *
     * @return float 
     */
    public function getReserve() {
        return $this->reserve;
    }

    /**
     * Set proReserve
     *
     * @param float $proReserve
     * @return Pratica
     */
    public function setProReserve($proReserve) {
        $this->proReserve = $proReserve;

        return $this;
    }

    /**
     * Get proReserve
     *
     * @return float 
     */
    public function getProReserve() {
        return $this->proReserve;
    }

    /**
     * Set dol
     *
     * @param \DateTime $dol
     * @return Pratica
     */
    public function setDol($dol) {
        $this->dol = $dol;

        return $this;
    }

    /**
     * Get dol
     *
     * @return \DateTime 
     */
    public function getDol() {
        return $this->dol;
    }

    /**
     * Set don
     *
     * @param \DateTime $don
     * @return Pratica
     */
    public function setDon($don) {
        $this->don = $don;

        return $this;
    }

    /**
     * Get don
     *
     * @return \DateTime 
     */
    public function getDon() {
        return $this->don;
    }

    /**
     * Set dataImport
     *
     * @param \DateTime $dataImport
     * @return Pratica
     */
    public function setDataImport($dataImport) {
        $this->dataImport = $dataImport;

        return $this;
    }

    /**
     * Get dataImport
     *
     * @return \DateTime 
     */
    public function getDataImport() {
        return $this->dataImport;
    }

    /**
     * Set auditText
     *
     * @param string $auditText
     * @return Pratica
     */
    public function setAuditText($auditText) {
        $this->auditText = $auditText;

        return $this;
    }

    /**
     * Get auditText
     *
     * @return string 
     */
    public function getAuditText() {
        return $this->auditText;
    }

    /**
     * Set azioni
     *
     * @param string $azioni
     * @return Pratica
     */
    public function setAzioni($azioni) {
        $this->azioni = $azioni;

        return $this;
    }

    /**
     * Get azioni
     *
     * @return string 
     */
    public function getAzioni() {
        return $this->azioni;
    }

    /**
     * Set percentuale
     *
     * @param integer $percentuale
     * @return Pratica
     */
    public function setPercentuale($percentuale) {
        $this->percentuale = $percentuale;

        return $this;
    }

    /**
     * Get percentuale
     *
     * @return integer 
     */
    public function getPercentuale() {
        return $this->percentuale;
    }

    /**
     * Set worstcaseScenario
     *
     * @param float $worstcaseScenario
     * @return Pratica
     */
    public function setWorstcaseScenario($worstcaseScenario) {
        $this->worstcaseScenario = $worstcaseScenario;

        return $this;
    }

    /**
     * Get worstcaseScenario
     *
     * @return float 
     */
    public function getWorstcaseScenario() {
        return $this->worstcaseScenario;
    }

    /**
     * Set proposedReserve
     *
     * @param float $proposedReserve
     * @return Pratica
     */
    public function setProposedReserve($proposedReserve) {
        $this->proposedReserve = $proposedReserve;

        return $this;
    }

    /**
     * Get proposedReserve
     *
     * @return float 
     */
    public function getProposedReserve() {
        return $this->proposedReserve;
    }

    /**
     * Set audit
     *
     * @param \Claims\HAuditBundle\Entity\Audit $audit
     * @return Pratica
     */
    public function setAudit(\Claims\HAuditBundle\Entity\Audit $audit = null) {
        $this->audit = $audit;

        return $this;
    }

    /**
     * Get audit
     *
     * @return \Claims\HAuditBundle\Entity\Audit 
     */
    public function getAudit() {
        return $this->audit;
    }

    public function getFact() {
        return $this->fact;
    }

    public function getInvestigationHandling() {
        return $this->investigationHandling;
    }

    public function getLiability() {
        return $this->liability;
    }

    public function getQuantum() {
        return $this->quantum;
    }

    public function getNegotiationHandling() {
        return $this->negotiationHandling;
    }

    public function getProceedingHandling() {
        return $this->proceedingHandling;
    }

    public function getOurComments() {
        return $this->ourComments;
    }

    public function getNlDamage() {
        return $this->nlDamage;
    }

    public function setFact($fact) {
        $this->fact = $fact;
        return $this;
    }

    public function setInvestigationHandling($investigationHandling) {
        $this->investigationHandling = $investigationHandling;
        return $this;
    }

    public function setLiability($liability) {
        $this->liability = $liability;
        return $this;
    }

    public function setQuantum($quantum) {
        $this->quantum = $quantum;
        return $this;
    }

    public function setNegotiationHandling($negotiationHandling) {
        $this->negotiationHandling = $negotiationHandling;
        return $this;
    }

    public function setProceedingHandling($proceedingHandling) {
        $this->proceedingHandling = $proceedingHandling;
        return $this;
    }

    public function setOurComments($ourComments) {
        $this->ourComments = $ourComments;
        return $this;
    }

    public function setNlDamage($nlDamage) {
        $this->nlDamage = $nlDamage;
        return $this;
    }

    /**
     * Add question
     *
     * @param \Claims\HAuditBundle\Entity\PraticaQuestion $question
     * @return Audit
     */
    public function addQuestion(\Claims\HAuditBundle\Entity\PraticaQuestion $question) {
        $this->question[] = $question;

        return $this;
    }

    /**
     * Remove question
     *
     * @param \Claims\HAuditBundle\Entity\PraticaQuestion $question
     */
    public function removeQuestion(\Claims\HAuditBundle\Entity\PraticaQuestion $question) {
        $this->question->removeElement($question);
    }

    /**
     * Get question
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestion() {
        return $this->question;
    }

    public function __toString() {
        return $this->getGruppo() . ' ' . $this->getClaimant();
    }

    /**
     * @param integer $id
     * @return PraticaQuestion|boolean
     */
    public function getValue($id) {
        foreach ($this->question as $question) {
            /* @var $question PraticaQuestion */
            if ($question->getQuestion()->getId() == $id) {
                return $question;
            }
        }
        return false;
    }

}
