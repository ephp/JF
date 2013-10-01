<?php

namespace Claims\HBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Pratica
 *
 * @ORM\Table(name="claims_h_pratiche")
 * @ORM\Entity(repositoryClass="Claims\HBundle\Entity\PraticaRepository")
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
     * @var Sistema
     * 
     * @ORM\ManyToOne(targetEntity="Sistema")
     * @ORM\JoinColumn(name="sistema_id", referencedColumnName="id")
     */
    private $sistema;

    /**
     * @var Ospedale
     *
     * @ORM\ManyToOne(targetEntity="Ospedale")
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
     * @ORM\Column(name="ro", type="boolean", nullable=true)
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

    /**
     * DATI STUDIO
     */

    /**
     * @var string
     *
     * @ORM\Column(name="settlement_authority", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $settlementAuthority;

    /**
     * @var string
     *
     * @ORM\Column(name="offerta_nostra", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $offertaNostra;

    /**
     * @var float
     *
     * @ORM\Column(name="offerta_loro", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $offertaLoro;

    /**
     * @var string
     *
     * @ORM\Column(name="recupero_offerta_nostra", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $recuperoOffertaNostra;

    /**
     * @var float
     *
     * @ORM\Column(name="recupero_offerta_loro", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $recuperoOffertaLoro;

    /**
     * @var text
     *
     * @ORM\Column(name="legali_avversari", type="text", nullable=true)
     */
    private $legaliAvversari;

    /**
     * @var text
     *
     * @ORM\Column(name="informazioni", type="text", nullable=true)
     */
    private $informazioni;

    /**
     * DATI REPORT
     */

    /**
     * @var string
     *
     * @ORM\Column(name="report_soi", type="string", length=2, nullable=true)
     */
    private $reportSoi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="report_dol", type="date", nullable=true)
     */
    private $reportDol;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="report_don", type="date", nullable=true)
     */
    private $reportDon;

    /**
     * @var float
     *
     * @ORM\Column(name="report_descrizione", type="text", nullable=true)
     */
    private $reportDescrizione;

    /**
     * @var float
     *
     * @ORM\Column(name="report_mpl", type="string", length=1, nullable=true)
     */
    private $reportMpl;

    /**
     * @var float
     *
     * @ORM\Column(name="report_giudiziale", type="text", nullable=true)
     */
    private $reportGiudiziale;

    /**
     * @var float
     *
     * @ORM\Column(name="report_other_policies", type="text", nullable=true)
     */
    private $reportOtherPolicies;

    /**
     * @var string
     *
     * @ORM\Column(name="report_type_of_loss", type="string", length=5, nullable=true)
     */
    private $reportTypeOfLoss;

    /**
     * @var string
     *
     * @ORM\Column(name="report_service_provider", type="string", length=5, nullable=true)
     */
    private $reportServiceProvider;

    /**
     * @var float
     *
     * @ORM\Column(name="report_possible_recovery", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $reportPossibleRecovery;

    /**
     * @var float
     *
     * @ORM\Column(name="report_amount_reserved", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $reportAmountReserved;

    /**
     * @var float
     *
     * @ORM\Column(name="report_applicable_deductable", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $reportApplicableDeductable;

    /**
     * @var float
     *
     * @ORM\Column(name="report_future_conduct", type="text", nullable=true)
     */
    private $reportFutureConduct;

    /**
     * @var \JF\ACLBundle\Entity\Gestore
     * 
     * @ORM\ManyToOne(targetEntity="JF\ACLBundle\Entity\Gestore")
     * @ORM\JoinColumn(name="report_gestore_id", referencedColumnName="id")
     */
    private $reportGestore;

    /**
     * @var float
     *
     * @ORM\Column(name="report_old", type="text", nullable=true)
     */
    private $reportOld;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Evento", mappedBy="pratica", cascade="all")
     * @ORM\OrderBy({"data_ora" = "ASC"})
     */
    private $eventi;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Link", mappedBy="pratica", cascade="all")
     * @ORM\OrderBy({"sito" = "ASC"})
     */
    private $links;

    /**
     * Constructor
     */
    public function __construct() {
        $this->links = new \Doctrine\Common\Collections\ArrayCollection();
        $this->eventi = new \Doctrine\Common\Collections\ArrayCollection();
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
    public function getTypeOfLoss($encode = false) {
        if ($encode) {
            $tpl = $this->tpl();
            return $tpl[intval($this->typeOfLoss)];
        }
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
    public function getSp($encode = false) {
        if ($encode) {
            $sa = $this->sa();
            return $sa[intval($this->sp)];
        }
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
    public function getMpl($encode = false) {
        if($encode) {
            $mpl = $this->fasceMpl();
            return $mpl[$this->mpl];
        }
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
    public function getSoi($encode = false) {
        if ($encode) {
            $soi = $this->severityOfInjury();
            return $soi[intval($this->soi)];
        }
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
    public function getAll($encode = false) {
        if ($encode) {
            $all = $this->allegati();
            if($this->all) {
                return $all[intval($this->all)];
            }
        }
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
     * @param boolean $ro
     * @return Pratica
     */
    public function setRo($ro) {
        $this->ro = $ro;

        return $this;
    }

    /**
     * Get ro
     *
     * @return boolean 
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
     * Set settlementAuthority
     *
     * @param float $settlementAuthority
     * @return Pratica
     */
    public function setSettlementAuthority($settlementAuthority) {
        $this->settlementAuthority = $settlementAuthority;

        return $this;
    }

    /**
     * Get settlementAuthority
     *
     * @return float 
     */
    public function getSettlementAuthority() {
        return $this->settlementAuthority;
    }

    /**
     * Set offerta_nostra
     *
     * @param float $offertaNostra
     * @return Pratica
     */
    public function setOffertaNostra($offertaNostra) {
        $this->offertaNostra = $offertaNostra;

        return $this;
    }

    /**
     * Get offerta_nostra
     *
     * @return float 
     */
    public function getOffertaNostra() {
        return $this->offertaNostra;
    }

    /**
     * Set offerta_loro
     *
     * @param float $offertaLoro
     * @return Pratica
     */
    public function setOffertaLoro($offertaLoro) {
        $this->offertaLoro = $offertaLoro;

        return $this;
    }

    /**
     * Get offerta_loro
     *
     * @return float 
     */
    public function getOffertaLoro() {
        return $this->offertaLoro;
    }

    /**
     * Set recupero_offerta_nostra
     *
     * @param float $recuperoOffertaNostra
     * @return Pratica
     */
    public function setRecuperoOffertaNostra($recuperoOffertaNostra) {
        $this->recuperoOffertaNostra = $recuperoOffertaNostra;

        return $this;
    }

    /**
     * Get recupero_offerta_nostra
     *
     * @return float 
     */
    public function getRecuperoOffertaNostra() {
        return $this->recuperoOffertaNostra;
    }

    /**
     * Set recupero_offerta_loro
     *
     * @param float $recuperoOffertaLoro
     * @return Pratica
     */
    public function setRecuperoOffertaLoro($recuperoOffertaLoro) {
        $this->recuperoOffertaLoro = $recuperoOffertaLoro;

        return $this;
    }

    /**
     * Get recupero_offerta_loro
     *
     * @return float 
     */
    public function getRecuperoOffertaLoro() {
        return $this->recuperoOffertaLoro;
    }

    /**
     * Set legali_avversari
     *
     * @param string $legaliAvversari
     * @return Pratica
     */
    public function setLegaliAvversari($legaliAvversari) {
        $this->legaliAvversari = $legaliAvversari;

        return $this;
    }

    /**
     * Get legali_avversari
     *
     * @return string 
     */
    public function getLegaliAvversari() {
        return $this->legaliAvversari;
    }

    /**
     * Set informazioni
     *
     * @param string $informazioni
     * @return Pratica
     */
    public function setInformazioni($informazioni) {
        $this->informazioni = $informazioni;

        return $this;
    }

    /**
     * Get informazioni
     *
     * @return string 
     */
    public function getInformazioni() {
        return $this->informazioni;
    }

    /**
     * Set report_soi
     *
     * @param string $reportSoi
     * @return Pratica
     */
    public function setReportSoi($reportSoi) {
        $this->reportSoi = $reportSoi;

        return $this;
    }

    /**
     * Get report_soi
     *
     * @return string 
     */
    public function getReportSoi($encode = false) {
        if ($encode) {
            $soi = $this->severityOfInjury();
            return $soi[intval($this->reportSoi)];
        }
        return $this->reportSoi;
    }

    /**
     * Set report_dol
     *
     * @param \DateTime $reportDol
     * @return Pratica
     */
    public function setReportDol($reportDol) {
        $this->reportDol = $reportDol;

        return $this;
    }

    /**
     * Get report_dol
     *
     * @return \DateTime 
     */
    public function getReportDol() {
        return $this->reportDol;
    }

    /**
     * Set report_don
     *
     * @param \DateTime $reportDon
     * @return Pratica
     */
    public function setReportDon($reportDon) {
        $this->reportDon = $reportDon;

        return $this;
    }

    /**
     * Get report_don
     *
     * @return \DateTime 
     */
    public function getReportDon() {
        return $this->reportDon;
    }

    /**
     * Set report_descrizione
     *
     * @param string $reportDescrizione
     * @return Pratica
     */
    public function setReportDescrizione($reportDescrizione) {
        $this->reportDescrizione = $reportDescrizione;

        return $this;
    }

    /**
     * Get report_descrizione
     *
     * @return string 
     */
    public function getReportDescrizione() {
        return $this->reportDescrizione;
    }

    /**
     * Set report_mpl
     *
     * @param string $reportMpl
     * @return Pratica
     */
    public function setReportMpl($reportMpl) {
        $this->reportMpl = $reportMpl;

        return $this;
    }

    /**
     * Get report_mpl
     *
     * @return string 
     */
    public function getReportMpl($encode = false) {
        if($encode) {
            $mpl = $this->fasceMpl();
            return $mpl[$this->reportMpl];
        }
        return $this->reportMpl;
    }

    /**
     * Set report_giudiziale
     *
     * @param string $reportGiudiziale
     * @return Pratica
     */
    public function setReportGiudiziale($reportGiudiziale) {
        $this->reportGiudiziale = $reportGiudiziale;

        return $this;
    }

    /**
     * Get report_giudiziale
     *
     * @return string 
     */
    public function getReportGiudiziale() {
        return $this->reportGiudiziale;
    }

    /**
     * Set report_other_policies
     *
     * @param string $reportOtherPolicies
     * @return Pratica
     */
    public function setReportOtherPolicies($reportOtherPolicies) {
        $this->reportOtherPolicies = $reportOtherPolicies;

        return $this;
    }

    /**
     * Get report_other_policies
     *
     * @return string 
     */
    public function getReportOtherPolicies() {
        return $this->reportOtherPolicies;
    }

    /**
     * Set report_type_of_loss
     *
     * @param string $reportTypeOfLoss
     * @return Pratica
     */
    public function setReportTypeOfLoss($reportTypeOfLoss) {
        $this->reportTypeOfLoss = $reportTypeOfLoss;

        return $this;
    }

    /**
     * Get report_type_of_loss
     *
     * @return string 
     */
    public function getReportTypeOfLoss() {
        return $this->reportTypeOfLoss;
    }

    /**
     * Set report_service_provider
     *
     * @param string $reportServiceProvider
     * @return Pratica
     */
    public function setReportServiceProvider($reportServiceProvider) {
        $this->reportServiceProvider = $reportServiceProvider;

        return $this;
    }

    /**
     * Get report_service_provider
     *
     * @return string 
     */
    public function getReportServiceProvider($encode = false) {
        if ($encode) {
            $sa = $this->sa();
            return $sa[intval($this->reportServiceProvider)];
        }
        return $this->reportServiceProvider;
    }

    /**
     * Set report_possible_recovery
     *
     * @param float $reportPossibleRecovery
     * @return Pratica
     */
    public function setReportPossibleRecovery($reportPossibleRecovery) {
        $this->reportPossibleRecovery = $reportPossibleRecovery;

        return $this;
    }

    /**
     * Get report_possible_recovery
     *
     * @return float 
     */
    public function getReportPossibleRecovery() {
        return $this->reportPossibleRecovery;
    }

    /**
     * Set report_amount_reserved
     *
     * @param float $reportAmountReserved
     * @return Pratica
     */
    public function setReportAmountReserved($reportAmountReserved) {
        $this->reportAmountReserved = $reportAmountReserved;

        return $this;
    }

    /**
     * Get report_amount_reserved
     *
     * @return float 
     */
    public function getReportAmountReserved() {
        return $this->reportAmountReserved;
    }

    /**
     * Set report_applicable_deductable
     *
     * @param float $reportApplicableDeductable
     * @return Pratica
     */
    public function setReportApplicableDeductable($reportApplicableDeductable) {
        $this->reportApplicableDeductable = $reportApplicableDeductable;

        return $this;
    }

    /**
     * Get report_applicable_deductable
     *
     * @return float 
     */
    public function getReportApplicableDeductable() {
        return $this->reportApplicableDeductable;
    }

    /**
     * Set report_future_conduct
     *
     * @param string $reportFutureConduct
     * @return Pratica
     */
    public function setReportFutureConduct($reportFutureConduct) {
        $this->reportFutureConduct = $reportFutureConduct;

        return $this;
    }

    /**
     * Get report_future_conduct
     *
     * @return string 
     */
    public function getReportFutureConduct() {
        return $this->reportFutureConduct;
    }

    /**
     * Set report_old
     *
     * @param string $reportOld
     * @return Pratica
     */
    public function setReportOld($reportOld) {
        $this->reportOld = $reportOld;

        return $this;
    }

    /**
     * Get report_old
     *
     * @return string 
     */
    public function getReportOld() {
        return $this->reportOld;
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
     * Set report_gestore
     *
     * @param \JF\ACLBundle\Entity\Gestore $reportGestore
     * @return Pratica
     */
    public function setReportGestore(\JF\ACLBundle\Entity\Gestore $reportGestore = null) {
        $this->reportGestore = $reportGestore;

        return $this;
    }

    /**
     * Get report_gestore
     *
     * @return \JF\ACLBundle\Entity\Gestore
     */
    public function getReportGestore() {
        return $this->reportGestore;
    }

    /**
     * Add eventi
     *
     * @param Wvwnro $eventi
     * @return Cliente
     */
    public function addEventi(Evento $eventi) {
        $this->eventi[] = $eventi;

        return $this;
    }

    /**
     * Remove eventi
     *
     * @param Evento $eventi
     */
    public function removeEventi(Evento $eventi) {
        $this->eventi->removeElement($eventi);
    }

    /**
     * Get eventi
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEventi() {
        return $this->eventi;
    }

    /**
     * Add links
     *
     * @param Wvwnro $links
     * @return Cliente
     */
    public function addLinks(Evento $links) {
        $this->links[] = $links;

        return $this;
    }

    /**
     * Remove links
     *
     * @param Evento $links
     */
    public function removeLinks(Evento $links) {
        $this->links->removeElement($links);
    }

    /**
     * Get links
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLinks() {
        return $this->links;
    }

    public function isGiudiziale() {
        return $this->court != ' ' && trim($this->court) != '';
    }

    public function __toString() {
        return $this->getCodice() . ' - ' . $this->getClaimant();
    }

    public function tpl() {
        return array(
            781 => "781 - TPL lesioni senza negligenza medica",
            787 => "787 - TPL negligenza medica coinvolta",
            780 => "780 - Danni alla proprietà",
            786 => "786 - Responsabilità datore di lavoro",
        );
    }

    public function sa() {
        return array(
            0 => '0 - Altri non classificati',
            101 => '101 - Cure infermieristiche',
            201 => '201 - Dermatologia',
            202 => '202 - Patologia',
            203 => '203 - Pediatria',
            204 => '204 - Psichiatria',
            205 => '205 - Riabilitazione chimica o fisica',
            301 => '301 - Anestesia',
            302 => '302 - Disturbi cardiovascolari o polmonari',
            303 => '303 - Endocrinologia',
            304 => '304 - Gastroenterologia',
            305 => '305 - Cure medico-generico',
            306 => '306 - Ematologia',
            307 => '307 - Medicina nucleare',
            308 => '308 - Oncologia',
            309 => '309 - Oculistica',
            310 => '310 - Otorinolaringoiatria',
            311 => '311 - Terapia, radiazioni, trauma',
            401 => '401 - Chirurgia del colon e del retto',
            402 => '402 - Chirurgia generale (altri non classificati)',
            403 => '403 - Cure intensive e critiche',
            404 => '404 - Chirurgia laringoiatrica',
            405 => '405 - Chirurgia urologica',
            501 => '501 - Indicenti ed emergenza',
            502 => '502 - Ostetricia',
            503 => '503 - Chirurgia neurologica',
            504 => '504 - Ostetricia e ginecologia',
            505 => '505 - Chirurgia ortopedica',
            506 => '506 - Chirurgia pediatrica',
            507 => '507 - Chirurgia estetica',
            508 => '508 - Chirurgia traumatologica',
        );
    }

    public function severityOfInjury() {
        return array(
            "9" => "9 - Solo emotivo - Spavento, nessun danno fisico.",
            "8" => "8 - Temporanei: Lieve - Lacerazioni, contusioni, ferite minori, eruzioni cutanee. Nessun ritardo.",
            "7" => "7 - Temporanei: Minore - Infezioni, frattura omessa, caduta in ospedale. Guarigione ritardata.",
            "6" => "6 - Temporanei: Maggiore - Ustioni, materiale chirurgico dimenticato, effetto da tossicodipendenza, danni al cervello. Guarigio",
            "5" => "5 - Permanente: Minori - Perdita di dita, perdita o danneggiamento di organi. Non comprende gli infortuni invalidanti.",
            "4" => "4 - Permanente: Significativo - Sordità, perdita di arti, perdita della vista, perdita di un rene o di un polmone.",
            "3" => "3 - Permanente: Maggiore - Paraplegia, cecità, perdita di due arti, danni al cervello.",
            "2" => "2 - Permanente: Grave - Quadraplegia, gravi danni al cervello, inabilità totale.",
            "1" => "1 - Permanente: Morte - ",
            "B" => "B - Baby Case - Neonato con 50% e oltre di disabilità.",
            "B1" => "B1 - Baby Case - Decesso.",
            "B2" => "B2 - Baby Case - Neonato con distocia alle spalle e/o danni minori a seguito parto.",
        );
    }
    
    public function fasceMpl() {
        return array(
            "A" => "da 800.000 in poi",
            "B" => "da 400.000 a 800.000",
            "C" => "da 150.000 a 400.000",
            "D" => "da 40.000 a 150.000",
            "E" => "da 10.000 a 40.000",
            "F" => "da 0 a 10.000",
        );
    }
    
    public function allegati() {
        return array(
            1 => "Provvedimenti opportuni non presi",
            2 => "Ritardo nella prestazione",
            3 => "Prestazioni improprie od errori",
            4 => "Procedura inutile o controindicata",
            5 => "Comunicazione o vigilanza",
            6 => "Continuità delle cure o gestione delle cure",
            7 => "Comportamento o Legale",
            8 => "Altri non classificati",
        );
    }

}