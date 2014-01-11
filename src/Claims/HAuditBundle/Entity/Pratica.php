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

    use \Claims\CoreBundle\Entity\Traits\Pratica,
        \Claims\HBundle\Entity\Traits\Pratica;

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
     * @var \Claims\HBundle\Entity\Sistema
     * 
     * @ORM\ManyToOne(targetEntity="Claims\HBundle\Entity\Sistema")
     * @ORM\JoinColumn(name="sistema_id", referencedColumnName="id")
     */
    private $sistema;

    /**
     * @var \Claims\HBundle\Entity\Ospedale
     *
     * @ORM\ManyToOne(targetEntity="Claims\HBundle\Entity\Ospedale")
     * @ORM\JoinColumn(name="ospedale_id", referencedColumnName="id")
     */
    private $ospedale;

    /**
     * @var integer
     *
     * @ORM\Column(name="anno", type="integer")
     */
    private $anno;

    /**
     * @var integer
     *
     * @ORM\Column(name="tpa", type="integer")
     */
    private $tpa;

    /**
     * @var string
     *
     * @ORM\Column(name="claimant", type="string", length=255)
     */
    private $claimant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dasc", type="date", nullable=true)
     */
    private $dasc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_import", type="date")
     */
    private $dataImport;

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
     * @var string
     *
     * @ORM\Column(name="type_of_loss", type="string", length=5, nullable=true)
     */
    private $typeOfLoss;

    /**
     * @var float
     *
     * @ORM\Column(name="first_reserve_indication", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $firstReserveIndication;

    /**
     * @var float
     *
     * @ORM\Column(name="applicable_deductible", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $applicableDeductible;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_reserved", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $amountReserved;

    /**
     * @var float
     *
     * @ORM\Column(name="deductible_reserved", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $deductibleReserved;

    /**
     * @var float
     *
     * @ORM\Column(name="lt_fees_reserve", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $ltFeesReserve;

    /**
     * @var float
     *
     * @ORM\Column(name="profess_fees_reserve", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $professFeesReserve;

    /**
     * @var float
     *
     * @ORM\Column(name="tpa_fees_reserve", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $tpaFeesReserve;

    /**
     * @var float
     *
     * @ORM\Column(name="possible_recovery", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $possibleRecovery;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_settled", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $amountSettled;

    /**
     * @var float
     *
     * @ORM\Column(name="deduc_paid", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $deducPaid;

    /**
     * @var float
     *
     * @ORM\Column(name="lt_fees_paid", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $ltFeesPaid;

    /**
     * @var float
     *
     * @ORM\Column(name="profess_fees_paid", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $professFeesPaid;

    /**
     * @var float
     *
     * @ORM\Column(name="tpa_fees_paid", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $tpaFeesPaid;

    /**
     * @var float
     *
     * @ORM\Column(name="total_paid", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $totalPaid;

    /**
     * @var float
     *
     * @ORM\Column(name="recovered", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $recovered;

    /**
     * @var float
     *
     * @ORM\Column(name="total_incurred", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $totalIncurred;

    /**
     * @var integer
     *
     * @ORM\Column(name="sp", type="integer", nullable=true)
     */
    private $sp;

    /**
     * @var string
     *
     * @ORM\Column(name="mpl", type="string", length=1, nullable=true)
     */
    private $mpl;

    /**
     * @var string
     *
     * @ORM\Column(name="soi", type="string", length=2, nullable=true)
     */
    private $soi;

    /**
     * @var string
     *
     * @ORM\Column(name="contec_all", type="string", length=4, nullable=true)
     */
    private $all;

    /**
     * @var string
     *
     * @ORM\Column(name="court", type="string", length=32, nullable=true)
     */
    private $court;

    /**
     * @var string
     *
     * @ORM\Column(name="req", type="string", length=4, nullable=true)
     */
    private $req;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="medical_examiner", type="date", nullable=true)
     */
    private $medicalExaminer;

    /**
     * @var string
     *
     * @ORM\Column(name="oth_pol", type="string", length=4, nullable=true)
     */
    private $othPol;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ro", type="string", length=1, nullable=true)
     */
    private $ro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="legal_team", type="date", nullable=true)
     */
    private $legalTeam;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text", nullable=true)
     */
    private $comments;

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
     * Constructor
     */
    public function __construct() {
        
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
     * @param integer $tpa
     * @return Pratica
     */
    public function setTpa($tpa) {
        $this->tpa = $tpa;

        return $this;
    }

    /**
     * Get tpa
     *
     * @return integer 
     */
    public function getTpa() {
        return $this->tpa;
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
     * Set dasc
     *
     * @param \DateTime $dasc
     * @return Pratica
     */
    public function setDasc($dasc) {
        $this->dasc = $dasc;

        return $this;
    }

    /**
     * Get dasc
     *
     * @return \DateTime 
     */
    public function getDasc() {
        return $this->dasc;
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
     * Set typeOfLoss
     *
     * @param string $typeOfLoss
     * @return Pratica
     */
    public function setTypeOfLoss($typeOfLoss) {
        $this->typeOfLoss = $typeOfLoss;

        return $this;
    }

    /**
     * Get typeOfLoss
     *
     * @return string 
     */
    public function getTypeOfLoss() {
        return $this->typeOfLoss;
    }

    /**
     * Set firstReserveIndication
     *
     * @param float $firstReserveIndication
     * @return Pratica
     */
    public function setFirstReserveIndication($firstReserveIndication) {
        $this->firstReserveIndication = $firstReserveIndication;

        return $this;
    }

    /**
     * Get firstReserveIndication
     *
     * @return float 
     */
    public function getFirstReserveIndication() {
        return $this->firstReserveIndication;
    }

    /**
     * Set applicableDeductible
     *
     * @param float $applicableDeductible
     * @return Pratica
     */
    public function setApplicableDeductible($applicableDeductible) {
        $this->applicableDeductible = $applicableDeductible;

        return $this;
    }

    /**
     * Get applicableDeductible
     *
     * @return float 
     */
    public function getApplicableDeductible() {
        return $this->applicableDeductible;
    }

    /**
     * Set amountReserved
     *
     * @param float $amountReserved
     * @return Pratica
     */
    public function setAmountReserved($amountReserved) {
        $this->amountReserved = $amountReserved;

        return $this;
    }

    /**
     * Get amountReserved
     *
     * @return float 
     */
    public function getAmountReserved() {
        return $this->amountReserved;
    }

    /**
     * Set deductibleReserved
     *
     * @param float $deductibleReserved
     * @return Pratica
     */
    public function setDeductibleReserved($deductibleReserved) {
        $this->deductibleReserved = $deductibleReserved;

        return $this;
    }

    /**
     * Get deductibleReserved
     *
     * @return float 
     */
    public function getDeductibleReserved() {
        return $this->deductibleReserved;
    }

    /**
     * Set ltFeesReserve
     *
     * @param float $ltFeesReserve
     * @return Pratica
     */
    public function setLtFeesReserve($ltFeesReserve) {
        $this->ltFeesReserve = $ltFeesReserve;

        return $this;
    }

    /**
     * Get ltFeesReserve
     *
     * @return float 
     */
    public function getLtFeesReserve() {
        return $this->ltFeesReserve;
    }

    /**
     * Set professFeesReserve
     *
     * @param float $professFeesReserve
     * @return Pratica
     */
    public function setProfessFeesReserve($professFeesReserve) {
        $this->professFeesReserve = $professFeesReserve;

        return $this;
    }

    /**
     * Get professFeesReserve
     *
     * @return float 
     */
    public function getProfessFeesReserve() {
        return $this->professFeesReserve;
    }

    /**
     * Set tpaFeesReserve
     *
     * @param float $tpaFeesReserve
     * @return Pratica
     */
    public function setTpaFeesReserve($tpaFeesReserve) {
        $this->tpaFeesReserve = $tpaFeesReserve;

        return $this;
    }

    /**
     * Get tpaFeesReserve
     *
     * @return float 
     */
    public function getTpaFeesReserve() {
        return $this->tpaFeesReserve;
    }

    /**
     * Set possibleRecovery
     *
     * @param float $possibleRecovery
     * @return Pratica
     */
    public function setPossibleRecovery($possibleRecovery) {
        $this->possibleRecovery = $possibleRecovery;

        return $this;
    }

    /**
     * Get possibleRecovery
     *
     * @return float 
     */
    public function getPossibleRecovery() {
        return $this->possibleRecovery;
    }

    /**
     * Set amountSettled
     *
     * @param float $amountSettled
     * @return Pratica
     */
    public function setAmountSettled($amountSettled) {
        $this->amountSettled = $amountSettled;

        return $this;
    }

    /**
     * Get amountSettled
     *
     * @return float 
     */
    public function getAmountSettled() {
        return $this->amountSettled;
    }

    /**
     * Set deducPaid
     *
     * @param float $deducPaid
     * @return Pratica
     */
    public function setDeducPaid($deducPaid) {
        $this->deducPaid = $deducPaid;

        return $this;
    }

    /**
     * Get deducPaid
     *
     * @return float 
     */
    public function getDeducPaid() {
        return $this->deducPaid;
    }

    /**
     * Set ltFeesPaid
     *
     * @param float $ltFeesPaid
     * @return Pratica
     */
    public function setLtFeesPaid($ltFeesPaid) {
        $this->ltFeesPaid = $ltFeesPaid;

        return $this;
    }

    /**
     * Get ltFeesPaid
     *
     * @return float 
     */
    public function getLtFeesPaid() {
        return $this->ltFeesPaid;
    }

    /**
     * Set professFeesPaid
     *
     * @param float $professFeesPaid
     * @return Pratica
     */
    public function setProfessFeesPaid($professFeesPaid) {
        $this->professFeesPaid = $professFeesPaid;

        return $this;
    }

    /**
     * Get professFeesPaid
     *
     * @return float 
     */
    public function getProfessFeesPaid() {
        return $this->professFeesPaid;
    }

    /**
     * Set tpaFeesPaid
     *
     * @param float $tpaFeesPaid
     * @return Pratica
     */
    public function setTpaFeesPaid($tpaFeesPaid) {
        $this->tpaFeesPaid = $tpaFeesPaid;

        return $this;
    }

    /**
     * Get tpaFeesPaid
     *
     * @return float 
     */
    public function getTpaFeesPaid() {
        return $this->tpaFeesPaid;
    }

    /**
     * Set totalPaid
     *
     * @param float $totalPaid
     * @return Pratica
     */
    public function setTotalPaid($totalPaid) {
        $this->totalPaid = $totalPaid;

        return $this;
    }

    /**
     * Get totalPaid
     *
     * @return float 
     */
    public function getTotalPaid() {
        return $this->totalPaid;
    }

    /**
     * Set recovered
     *
     * @param float $recovered
     * @return Pratica
     */
    public function setRecovered($recovered) {
        $this->recovered = $recovered;

        return $this;
    }

    /**
     * Get recovered
     *
     * @return float 
     */
    public function getRecovered() {
        return $this->recovered;
    }

    /**
     * Set totalIncurred
     *
     * @param float $totalIncurred
     * @return Pratica
     */
    public function setTotalIncurred($totalIncurred) {
        $this->totalIncurred = $totalIncurred;

        return $this;
    }

    /**
     * Get totalIncurred
     *
     * @return float 
     */
    public function getTotalIncurred() {
        return $this->totalIncurred;
    }

    /**
     * Set sp
     *
     * @param integer $sp
     * @return Pratica
     */
    public function setSp($sp) {
        $this->sp = $sp;

        return $this;
    }

    /**
     * Get sp
     *
     * @return integer 
     */
    public function getSp() {
        return $this->sp;
    }

    /**
     * Set mpl
     *
     * @param string $mpl
     * @return Pratica
     */
    public function setMpl($mpl) {
        $this->mpl = $mpl;

        return $this;
    }

    /**
     * Get mpl
     *
     * @return string 
     */
    public function getMpl() {
        return $this->mpl;
    }

    /**
     * Set soi
     *
     * @param string $soi
     * @return Pratica
     */
    public function setSoi($soi) {
        $this->soi = $soi;

        return $this;
    }

    /**
     * Get soi
     *
     * @return string 
     */
    public function getSoi() {
        return $this->soi;
    }

    /**
     * Set all
     *
     * @param string $all
     * @return Pratica
     */
    public function setAll($all) {
        $this->all = $all;

        return $this;
    }

    /**
     * Get all
     *
     * @return string 
     */
    public function getAll() {
        return $this->all;
    }

    /**
     * Set court
     *
     * @param string $court
     * @return Pratica
     */
    public function setCourt($court) {
        $this->court = $court;

        return $this;
    }

    /**
     * Get court
     *
     * @return string 
     */
    public function getCourt() {
        return $this->court;
    }

    /**
     * Set req
     *
     * @param string $req
     * @return Pratica
     */
    public function setReq($req) {
        $this->req = $req;

        return $this;
    }

    /**
     * Get req
     *
     * @return string 
     */
    public function getReq() {
        return $this->req;
    }

    /**
     * Set medicalExaminer
     *
     * @param \DateTime $medicalExaminer
     * @return Pratica
     */
    public function setMedicalExaminer($medicalExaminer) {
        $this->medicalExaminer = $medicalExaminer;

        return $this;
    }

    /**
     * Get medicalExaminer
     *
     * @return \DateTime 
     */
    public function getMedicalExaminer() {
        return $this->medicalExaminer;
    }

    /**
     * Set othPol
     *
     * @param string $othPol
     * @return Pratica
     */
    public function setOthPol($othPol) {
        $this->othPol = $othPol;

        return $this;
    }

    /**
     * Get othPol
     *
     * @return string 
     */
    public function getOthPol() {
        return $this->othPol;
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
     * Set ro
     *
     * @param string $ro
     * @return Pratica
     */
    public function setRo($ro) {
        $this->ro = $ro;

        return $this;
    }

    /**
     * Get ro
     *
     * @return string 
     */
    public function getRo() {
        return $this->ro;
    }

    /**
     * Set legalTeam
     *
     * @param \DateTime $legalTeam
     * @return Pratica
     */
    public function setLegalTeam($legalTeam) {
        $this->legalTeam = $legalTeam;

        return $this;
    }

    /**
     * Get legalTeam
     *
     * @return \DateTime 
     */
    public function getLegalTeam() {
        return $this->legalTeam;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return Pratica
     */
    public function setComments($comments) {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments() {
        return $this->comments;
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
     * Get audit
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
     * Set sistema
     *
     * @param \Claims\HBundle\Entity\Sistema $sistema
     * @return Pratica
     */
    public function setSistema(\Claims\HBundle\Entity\Sistema $sistema = null) {
        $this->sistema = $sistema;

        return $this;
    }

    /**
     * Get sistema
     *
     * @return \Claims\HBundle\Entity\Sistema 
     */
    public function getSistema() {
        return $this->sistema;
    }

    /**
     * Set ospedale
     *
     * @param \Claims\HBundle\Entity\Ospedale $ospedale
     * @return Pratica
     */
    public function setOspedale(\Claims\HBundle\Entity\Ospedale $ospedale = null) {
        $this->ospedale = $ospedale;

        return $this;
    }

    /**
     * Get ospedale
     *
     * @return \Claims\HBundle\Entity\Ospedale 
     */
    public function getOspedale() {
        return $this->ospedale;
    }

    /**
     * Set audit
     *
     * @param \Claims\HAuditBundle\Entity\Audit $audit
     * @return Pratica
     */
    public function setAudit(\Claims\HAuditBundle\Entity\Audit $audit = null)
    {
        $this->audit = $audit;
    
        return $this;
    }

    /**
     * Get audit
     *
     * @return \Claims\HAuditBundle\Entity\Audit 
     */
    public function getAudit()
    {
        return $this->audit;
    }

    public function __toString() {
        return $this->getCodice() . ' - ' . $this->getClaimant();
    }

}