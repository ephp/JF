<?php

namespace Claims\HBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\Table(name="claims_h_report")
 * @ORM\Entity(repositoryClass="Claims\HBundle\Entity\ReportRepository")
 */
class Report
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
     * @ORM\ManyToOne(targetEntity="Pratica")
     * @ORM\JoinColumn(name="pratica_id", referencedColumnName="id")
     */
    private $pratica;

    /**
     * @var date
     *
     * @ORM\Column(name="data", type="date")
     */
    private $data;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="copertura", type="text", nullable=true)
     */
    private $copertura;

    /**
     * @var string
     *
     * @ORM\Column(name="stato", type="text", nullable=true)
     */
    private $stato;

    /**
     * @var string
     *
     * @ORM\Column(name="descrizione_in_fatto", type="text", nullable=true)
     */
    private $descrizione_in_fatto;

    /**
     * @var string
     *
     * @ORM\Column(name="relazione_avversaria", type="text", nullable=true)
     */
    private $relazione_avversaria;

    /**
     * @var string
     *
     * @ORM\Column(name="relazione_ex_adverso", type="text", nullable=true)
     */
    private $relazione_ex_adverso;

    /**
     * @var string
     *
     * @ORM\Column(name="valutazione_responsabilita", type="text", nullable=true)
     */
    private $valutazione_responsabilita;

    /**
     * @var string
     *
     * @ORM\Column(name="analisi_danno", type="text", nullable=true)
     */
    private $analisi_danno;

    /**
     * @var string
     *
     * @ORM\Column(name="riserva", type="text", nullable=true)
     */
    private $riserva;

    /**
     * @var string
     *
     * @ORM\Column(name="possibile_rivalsa", type="text", nullable=true)
     */
    private $possibile_rivalsa;

    /**
     * @var string
     *
     * @ORM\Column(name="azioni", type="text", nullable=true)
     */
    private $azioni;

    /**
     * @var string
     *
     * @ORM\Column(name="richiesta_sa", type="text", nullable=true)
     */
    private $richiesta_sa;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="medico_legale1", type="text", nullable=true)
     */
    private $medico_legale1;

    /**
     * @var string
     *
     * @ORM\Column(name="medico_legale2", type="text", nullable=true)
     */
    private $medico_legale2;

    /**
     * @var string
     *
     * @ORM\Column(name="medico_legale3", type="text", nullable=true)
     */
    private $medico_legale3;
    
    /**
     * @var string
     *
     * @ORM\Column(name="validato", type="boolean")
     */
    private $validato;
    
    /**
     * Set scheda
     *
     * @param string $pratica
     * @return Report
     */
    public function setPratica($pratica)
    {
        $this->pratica = $pratica;
    
        return $this;
    }

    /**
     * Get scheda
     *
     * @return Pratica 
     */
    public function getPratica()
    {
        return $this->pratica;
    }

    /**
     * Set number
     *
     * @param integer $number
     * @return Report
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set copertura
     *
     * @param string $copertura
     * @return Report
     */
    public function setCopertura($copertura)
    {
        $this->copertura = $copertura;
    
        return $this;
    }

    /**
     * Get copertura
     *
     * @return string 
     */
    public function getCopertura()
    {
        return $this->copertura;
    }

    /**
     * Set stato
     *
     * @param string $stato
     * @return Report
     */
    public function setStato($stato)
    {
        $this->stato = $stato;
    
        return $this;
    }

    /**
     * Get stato
     *
     * @return string 
     */
    public function getStato()
    {
        return $this->stato;
    }

    /**
     * Set descrizione_in_fatto
     *
     * @param string $descrizioneInFatto
     * @return Report
     */
    public function setDescrizioneInFatto($descrizioneInFatto)
    {
        $this->descrizione_in_fatto = $descrizioneInFatto;
    
        return $this;
    }

    /**
     * Get descrizione_in_fatto
     *
     * @return string 
     */
    public function getDescrizioneInFatto()
    {
        return $this->descrizione_in_fatto;
    }

    /**
     * Set relazione_avversaria
     *
     * @param string $relazioneAvversaria
     * @return Report
     */
    public function setRelazioneAvversaria($relazioneAvversaria)
    {
        $this->relazione_avversaria = $relazioneAvversaria;
    
        return $this;
    }

    /**
     * Get relazione_avversaria
     *
     * @return string 
     */
    public function getRelazioneAvversaria()
    {
        return $this->relazione_avversaria;
    }

    /**
     * Set relazione_ex_adverso
     *
     * @param string $relazioneExAdverso
     * @return Report
     */
    public function setRelazioneExAdverso($relazioneExAdverso)
    {
        $this->relazione_ex_adverso = $relazioneExAdverso;
    
        return $this;
    }

    /**
     * Get relazione_ex_adverso
     *
     * @return string 
     */
    public function getRelazioneExAdverso()
    {
        return $this->relazione_ex_adverso;
    }

    /**
     * Set valutazione_responsabilita
     *
     * @param string $valutazioneResponsabilita
     * @return Report
     */
    public function setValutazioneResponsabilita($valutazioneResponsabilita)
    {
        $this->valutazione_responsabilita = $valutazioneResponsabilita;
    
        return $this;
    }

    /**
     * Get valutazione_responsabilita
     *
     * @return string 
     */
    public function getValutazioneResponsabilita()
    {
        return $this->valutazione_responsabilita;
    }

    /**
     * Set analisi_danno
     *
     * @param string $analisiDanno
     * @return Report
     */
    public function setAnalisiDanno($analisiDanno)
    {
        $this->analisi_danno = $analisiDanno;
    
        return $this;
    }

    /**
     * Get analisi_danno
     *
     * @return string 
     */
    public function getAnalisiDanno()
    {
        return $this->analisi_danno;
    }

    /**
     * Set riserva
     *
     * @param string $riserva
     * @return Report
     */
    public function setRiserva($riserva)
    {
        $this->riserva = $riserva;
    
        return $this;
    }

    /**
     * Get riserva
     *
     * @return string 
     */
    public function getRiserva()
    {
        return $this->riserva;
    }

    /**
     * Set possibile_rivalsa
     *
     * @param string $possibileRivalsa
     * @return Report
     */
    public function setPossibileRivalsa($possibileRivalsa)
    {
        $this->possibile_rivalsa = $possibileRivalsa;
    
        return $this;
    }

    /**
     * Get possibile_rivalsa
     *
     * @return string 
     */
    public function getPossibileRivalsa()
    {
        return $this->possibile_rivalsa;
    }

    /**
     * Set azioni
     *
     * @param string $azioni
     * @return Report
     */
    public function setAzioni($azioni)
    {
        $this->azioni = $azioni;
    
        return $this;
    }

    /**
     * Get azioni
     *
     * @return string 
     */
    public function getAzioni()
    {
        return $this->azioni;
    }

    /**
     * Set richiesta_sa
     *
     * @param string $richiestaSa
     * @return Report
     */
    public function setRichiestaSa($richiestaSa)
    {
        $this->richiesta_sa = $richiestaSa;
    
        return $this;
    }

    /**
     * Get richiesta_sa
     *
     * @return string 
     */
    public function getRichiestaSa()
    {
        return $this->richiesta_sa;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Report
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }
    
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
     * Set validato
     *
     * @param boolean $validato
     * @return Report
     */
    public function setValidato($validato)
    {
        $this->validato = $validato;
    
        return $this;
    }

    /**
     * Get validato
     *
     * @return boolean 
     */
    public function getValidato()
    {
        return $this->validato;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     * @return Report
     */
    public function setData($data)
    {
        $this->data = $data;
    
        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set medico_legale1
     *
     * @param string $medicoLegale1
     * @return Report
     */
    public function setMedicoLegale1($medicoLegale1)
    {
        $this->medico_legale1 = $medicoLegale1;
    
        return $this;
    }

    /**
     * Get medico_legale1
     *
     * @return string 
     */
    public function getMedicoLegale1()
    {
        return $this->medico_legale1;
    }

    /**
     * Set medico_legale2
     *
     * @param string $medicoLegale2
     * @return Report
     */
    public function setMedicoLegale2($medicoLegale2)
    {
        $this->medico_legale2 = $medicoLegale2;
    
        return $this;
    }

    /**
     * Get medico_legale2
     *
     * @return string 
     */
    public function getMedicoLegale2()
    {
        return $this->medico_legale2;
    }

    /**
     * Set medico_legale3
     *
     * @param string $medicoLegale3
     * @return Report
     */
    public function setMedicoLegale3($medicoLegale3)
    {
        $this->medico_legale3 = $medicoLegale3;
    
        return $this;
    }

    /**
     * Get medico_legale3
     *
     * @return string 
     */
    public function getMedicoLegale3()
    {
        return $this->medico_legale3;
    }
}